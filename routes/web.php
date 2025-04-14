<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\OrderBrowserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/create-user', [RegisteredUserController::class, 'create'])->name('admin.create-user');
    Route::post('/admin/create-user', [RegisteredUserController::class, 'store'])->name('admin.store-user');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

});

Route::get('/upload-zip', function () {
    $csrf = csrf_token(); // Get the CSRF token for the form
    return <<<HTML
    <form method="POST" action="/upload-zip" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{$csrf}">
        <input type="file" name="zip_file" required>
        <button type="submit">Upload ZIP</button>
    </form>
    HTML;
});

//Route::post('/upload-zip', function (Request $request) {
//    $request->validate([
//        'zip_file' => 'required|file|mimes:zip',
//    ]);
//
//    $zipFile = $request->file('zip_file');
//    $zipFileName = $zipFile->getClientOriginalName();
//    $zipPath = storage_path('app/temp/' . $zipFileName);
//
//    // Move the uploaded ZIP to a temp location
//    $zipFile->move(storage_path('app/temp'), $zipFileName);
//
//    $zip = new ZipArchive;
//    if ($zip->open($zipPath) === TRUE) {
//        $extractPath = storage_path('app/unzipped/' . pathinfo($zipFileName, PATHINFO_FILENAME));
//        dd($extractPath);
//        $zip->extractTo($extractPath);
//        $zip->close();
//
//        return "ZIP file extracted to: <code>{$extractPath}</code>";
//    } else {
//        return "Failed to open the ZIP file.";
//    }
//});
Route::post('/upload-zip', function (Request $request) {
    $request->validate([
        'title' => 'required|string',
        'description' => 'nullable|string',
        'zip_file' => 'required|file|mimes:zip',
    ]);

    $order = Order::create([
        'title' => $request->title,
        'description' => $request->description,
        'status' => 'pending',
        'created_by' => auth()->id(),
    ]);

    $zipFile = $request->file('zip_file');
    $zipFileName = $zipFile->getClientOriginalName();
    $zipPath = storage_path('app/temp/' . $zipFileName);

    $zipFile->move(storage_path('app/temp'), $zipFileName);

    $zip = new ZipArchive;
    if ($zip->open($zipPath) === TRUE) {
        // Avoid folder name collision
        $extractPath = storage_path('app/unzipped/' . $request->title);

        if (!file_exists($extractPath)) {
            mkdir($extractPath, 0755, true);
        }

        $zip->extractTo($extractPath);
        $zip->close();

        return "ZIP file extracted to: <code>{$extractPath}</code>";
    } else {
        return "Failed to open the ZIP file.";
    }
});
require __DIR__.'/auth.php';

<x-app-layout>
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h2 class="text-2xl font-bold text-gray-800 pb-4">Create Order</h2>
    <form method="POST" action="{{ route('orders.store') }}" enctype="multipart/form-data">
        @csrf

        <div>
            <x-input-label for="title" value="Order Title" />
            <x-text-input name="title" id="title" required class="block mt-1 w-full"/>
        </div>

        <div class="mt-4">
            <x-input-label for="description" value="Description" />
            <textarea name="description" id="description" class="block w-full mt-1 rounded-md border-gray-300"></textarea>
        </div>

        <div class="mt-4">
            <x-input-label for="zip_file" value="Upload ZIP File" />
            <input type="file" name="zip_file" id="zip_file" accept=".zip" required class="block mt-1 w-full"/>
        </div>

        <x-primary-button class="mt-4">Create Order</x-primary-button>
    </form>
    </div>
</x-app-layout>

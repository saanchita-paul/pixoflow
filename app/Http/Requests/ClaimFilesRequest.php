<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimFilesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file_ids' => ['array'],
            'file_ids.*' => ['exists:files,id'],
            'folder_paths' => ['array'],
            'folder_paths.*' => ['string'],
        ];
    }
}

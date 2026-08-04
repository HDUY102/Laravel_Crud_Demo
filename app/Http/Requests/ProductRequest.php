<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all users to make this request
        // return false; // Deny all users from making this request
        // return auth()->user()->isAdmin();  // Allow admin to make this request
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        return [
            'nameProduct' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'price' => [$isUpdate ? 'sometimes' : 'required', 'numeric', 'min:0'],
            'status' => ['nullable', 'integer', 'in:0,1'],
            'image' => ['nullable', 'string'],
        ];
    }
}

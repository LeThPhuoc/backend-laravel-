<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class EditProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'address' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'nullable|string',
            'end_date' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập vào Tên dự án.',
            'address.required' => 'Bạn chưa nhập vào Địa chỉ dự án.',
            'description.required' => 'Bạn chưa nhập Mô tả.',
        ];
    }
}
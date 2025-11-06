<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
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
            'name' => 'required|string|unique:users,name',
            'description' => 'required|string',
            'start_date' => 'nullable|string',
            'end_date' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập vào Tên.',
            'name.unique' => 'Tên đã được tạo trước đó.',
            'description.required' => 'Bạn chưa nhập Mô tả.',
        ];
    }
}
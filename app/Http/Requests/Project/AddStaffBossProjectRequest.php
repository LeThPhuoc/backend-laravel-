<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class AddStaffBossProjectRequest extends FormRequest
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
            'staffs' => ['nullable','array'],
            'staffs.*' => ['required', 'array'],
            'staffs.*.id' => ['required', 'string'],
            'staffs.*.salary' => ['required', 'string'],
            'staffs.*.role' => ['required', 'string'],
            'bosses' => ['nullable','array'],
            'bosses.*' => ['required', 'array'],
            'bosses.*.id' => ['required', 'string'],
            'bosses.*.role' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            // 'name.required' => 'Bạn chưa nhập vào Tên dự án.',
            // 'name.unique' => 'Tên dự án đã được tạo trước đó.',
            // 'address.required' => 'Bạn chưa nhập vào Địa chỉ dự án.',
            // 'address.unique' => 'Địa chỉ dự án đã được tạo trước đó.',
            // 'description.required' => 'Bạn chưa nhập Mô tả.',
        ];
    }
}
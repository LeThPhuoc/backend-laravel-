<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class DeleteStaffBossFromProjectRequest extends FormRequest
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
            'staff_id' => ['nullable','array'],
            'staffs.*' => ['required', 'array'],
            'staffs.*.id' => ['required', 'string'],
            'boss_id' => ['nullable','array'],
            'bosss.*' => ['required', 'array'],
            'bosss.*.id' => ['required', 'string']
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
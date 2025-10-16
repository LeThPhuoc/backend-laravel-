<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthStore extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'name' => 'required',
            'login_name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'tel' => 'required|max:11',
            'basic_salary' => 'nullable',
            'password' => 'required',
            'address' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Bạn chưa nhập vào email.',
            'email.email' => 'Email chưa đúng định dạng. Ví dụ: abc@gmail.com',
            'email.unique' => 'Email đã tồn tại. Vui lòng chọn email khác',
            'password.required' => 'Bạn chưa nhập vào mật khẩu.',
            'name.required' => 'Bạn chưa nhập Tên.',
            'login_name.required' => 'Bạn chưa nhập Tên Đăng Nhập.',
            'login_name.unique' => 'Tên Đăng Nhập đã tồn tại.',
            'tel.required' => 'Bạn chưa nhập Số Điện Thoại.',
            'address.required' => 'Bạn chưa nhập Địa Chỉ.',
        ];
    }
}

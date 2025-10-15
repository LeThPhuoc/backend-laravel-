<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\AuthLoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

class AuthControllerApi extends Controller
{
    public function __construct(){

    }

    public function index(){
        
    }

    public function register(){
        
    }

    public function login(AuthLoginRequest $request){
        $credentials = [
            'login_name' => $request->input('login_name'),
            'password' => $request->input('password')
        ];

        if ($token = auth('api')->attempt($credentials)) {
            return response()->json([
                'token' => $token,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'type' => "bearer",
                'user' => auth('api')->user()
            ]);
        }

        return response()->json([
            'message' => 'Thông tin đăng nhập không hợp lệ.',
            'errors' => [
                'login_name' => ['Thông tin tài khoản hoặc mật khẩu không đúng.'],
            ]
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function store(AuthRequest $request){
        $post = $request->only('name', 'login_name', 'tel', 'password', 'address', 'email', 'basic_salary');
        $isAdmin = $request->has('is_admin') ? 1 : 0;
        $post = User::create([
            'name' => $post['name'],
            'login_name' => $post['login_name'],
            'tel' => $post['tel'],
            'password' => Hash::make($post['password']),
            'address' => $post['address'],
            'email' => $post['email'],
            'basic_salary' => $post['basic_salary'],
            'is_admin' => $isAdmin
        ]);
        return response()->json([
            'message' => 'Successfully created user!'
        ]);
    }

    public function logout(){
        auth('api')->logout();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}

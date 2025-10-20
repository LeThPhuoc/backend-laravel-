<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthStore;
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

        return $this->handleLoginFailure($request);
    }

    public function store(AuthStore $request){
        $post = $request->only('name', 'login_name', 'tel', 'password', 'address', 'email', 'is_admin');
        $post = User::create([
            'name' => $post['name'],
            'login_name' => $post['login_name'],
            'tel' => $post['tel'],
            'password' => Hash::make($post['password']),
            'address' => $post['address'],
            'email' => $post['email'],
            'is_admin' => $post['is_admin']
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

    private function handleLoginFailure(AuthLoginRequest $request) {
        $userName = $request->input('login_name');
        $user = User::where('login_name', $userName)->first();
        if($user) {
            return response()->json([
                'message' => 'Mật khẩu không đúng.',
                'errors' => [
                    'password' => ['Mật khẩu không đúng.'],
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            return response()->json([
                'message' => 'Thông tin đăng nhập không hợp lệ.',
                'errors' => [
                    'login_name' => ['Tên đăng nhập hoặc mật khẩu không đúng.'],
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
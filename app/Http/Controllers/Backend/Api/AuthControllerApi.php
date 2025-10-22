<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthStore;
use App\Http\Requests\AuthLoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Boss;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

class AuthControllerApi extends Controller
{
    public function __construct(){

    }

    public function login(AuthLoginRequest $request){
        $credentials = [
            'login_name' => $request->input('login_name'),
            'password' => $request->input('password')
        ];
        if ($token = auth('boss')->attempt($credentials)) {
            return response()->json([
                'token' => $token,
                'expires_in' => auth('boss')->factory()->getTTL() * 60,
                'type' => "bearer",
                'user' => auth('boss')->user(),
                'role' => 'boss'
            ]);
        }
        if ($token = auth('staff')->attempt($credentials)) {
            return response()->json([
                'token' => $token,
                'expires_in' => auth('staff')->factory()->getTTL() * 60,
                'type' => "bearer",
                'user' => auth('staff')->user(),
                'role' => 'staff'
            ]);
        }

        return $this->handleLoginFailure($request);
    }

    public function store(AuthStore $request){
        $post = $request->only('name', 'login_name', 'tel', 'password', 'address', 'email');
        $exists = Boss::where('email', $post['email'])->orWhere('login_name', $post['login_name'])->exists() ||
                Staff::where('email', $post['email'])->orWhere('login_name', $post['login_name'])->exists();

        if ($exists) {
            return response()->json(['message' => ['Tên đăng nhập hoặc email đã tồn tại']], 400);
        }
        $post = Boss::create([
            'name' => $post['name'],
            'login_name' => $post['login_name'],
            'tel' => $post['tel'],
            'password' => Hash::make($post['password']),
            'address' => $post['address'],
            'email' => $post['email'],
        ]);
        return response()->json([
            'message' => ['Successfully created account!']
        ]);
    }

    public function createStaff(AuthStore $request){
        $post = $request->only('name', 'login_name', 'tel', 'password', 'address', 'email', 'salary');
        $boss = auth('boss')->user();
        $exists = Staff::where('email', $post['email'])->orWhere('login_name', $post['login_name'])->exists();

        if ($exists) {
            return response()->json(['message' => ['Tên đăng nhập hoặc email đã tồn tại']], 400);
        }

        $post = Staff::create([
            'name' => $post['name'],
            'login_name' => $post['login_name'],
            'tel' => $post['tel'],
            'password' => Hash::make($post['password']),
            'address' => $post['address'],
            'email' => $post['email'],
        ]);

        $boss->staffs()->attach($post->id, ['salary' => $post['salary']]);
        
        return response()->json([
            'message' => ['Successfully created account!']
        ]);
    }

    public function getListStaff() {
        $boss = auth('boss')->user();
        $staffs = Boss::with('staffs')->findOrFail($boss->id);
        foreach($staffs->staffs as $staff) {
            $staff->salary = $staff->pivot->salary;
            unset($staff->pivot);
            unset($staff->email_verified_at);
            unset($staff->remember_token);
            unset($staff->created_at);
            unset($staff->updated_at);
        }
        return response()->json([
            'staffs' => $staffs->staffs
        ]);
    }

    public function logout(){
        auth('api')->logout();
        return response()->json([
            'message' => ['Successfully logged out']
        ]);
    }

    private function handleLoginFailure(AuthLoginRequest $request) {
        $userName = $request->input('login_name');
        $user = Boss::where('login_name', $userName)->first() || Staff::where('login_name', $userName)->first();
        if($user) {
            return response()->json([
                'message' => ['Mật khẩu không đúng.'],
                'errors' => [
                    'password' => ['Mật khẩu không đúng.'],
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            return response()->json([
                'message' => ['Thông tin đăng nhập không hợp lệ.'],
                'errors' => [
                    'login_name' => ['Tên đăng nhập hoặc mật khẩu không đúng.'],
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}

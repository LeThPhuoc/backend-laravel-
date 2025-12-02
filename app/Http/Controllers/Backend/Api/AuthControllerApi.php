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

    protected $guards = ['boss', 'staff'];

    public function login(AuthLoginRequest $request){
        $credentials = [
            'login_name' => $request->input('login_name'),
            'password' => $request->input('password')
        ];

        $boss = Boss::where('login_name', $request->input('login_name'))->exists();
        $staff = Staff::where('login_name', $request->input('login_name'))->exists();

        if($boss) {
            if ($token = auth('boss')->attempt($credentials)) {
                return response()->json([
                    'token' => $token,
                    'type' => "bearer",
                    'user' => auth('boss')->user(),
                    'role' => 'boss'
                ]);
            }
        }
        if($staff) {
            if ($token = auth('staff')->attempt($credentials)) {
                return response()->json([
                    'token' => $token,
                    'type' => "bearer",
                    'user' => auth('staff')->user(),
                    'role' => 'staff'
                ]);
            }
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

    public function createStaff(AuthStore $request, $bossId){
        $post = $request->only('name', 'login_name', 'tel', 'password', 'address', 'email');
        $exists = Boss::where('email', $post['email'])->orWhere('login_name', $post['login_name'])->exists() ||
                Staff::where('email', $post['email'])->orWhere('login_name', $post['login_name'])->exists();

        if ($exists) {
            return response()->json(['message' => ['Tên đăng nhập hoặc email đã tồn tại']], 400);
        }

        $staff = Staff::create([
            'name' => $post['name'],
            'login_name' => $post['login_name'],
            'tel' => $post['tel'],
            'password' => Hash::make($post['password']),
            'address' => $post['address'],
            'email' => $post['email'],
        ]);

        $staff->bosses()->attach($bossId);
        
        return response()->json($staff);
    }

    public function getListStaff(Request $request, $bossId) {
        $perPage = $request->input('per_page', 20);
        $search = $request->query('search');
        $boss = Boss::findOrFail($bossId);
        $staffs = $boss->staffs()
        ->when($search, function ($q) use ($search) {
            $q->where('name', 'LIKE', "%$search%");
        })
        ->paginate($perPage);
        return response()->json([
            'data' => $staffs->items(),
            'page' => $staffs->currentPage(),
            'last_page' => $staffs->lastPage()
        ]);
    }

    public function logout(){
        foreach($this->guards as $guard) {
            auth($guard)->logout();
        }
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

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return response([
            'success' => true,
            'results' => $request->user()
        ], 200);
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response([
            'success' => true,
            'token' => $token,
            'results' => $user
        ], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }
        $user->tokens()->delete();
    
        $token = $user->createToken($request->device_name)->plainTextToken;
        return response([
            'success' => true,
            'token' => $token,
            'results' => $user
        ], 200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|confirmed'
        ]);

        $user = $request->user();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response([
            'success' => true,
            'results' => $user
        ], 200);


    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response('logout', 200);
    }

    public function userList(Request $request)
    {

        return response([
            'success' => true,
            'results' => User::skip($request->skip)->take($request->take)->latest()->get()
        ]);
    }
    public function findUser($key)
    {
        $key = htmlspecialchars($key);

        return response([
            'success' => true,
            'results' => User::where('name', 'like', '%' . $key . '%')
                ->orWhere('email', 'like', '%' . $key . '%')
                ->orWhere('phone', 'like', '%' . $key . '%')
                ->get()
        ]);
    }
    
}

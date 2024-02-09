<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User\UserAuthResource;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'exists:users,email'],
            'password' => ['required', 'max:50'],
        ]);

        if (!Auth::attempt($credentials)) {
            return $this->fail('Password is not correct.');
        }

        $user = Auth::user();
        return $this->success('user logged in successfully', new UserAuthResource($user));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6', 'max:50'],
        ]);

        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);
            DB::commit();
            Auth::loginUsingId($user->id);
            return $this->success('register user is successfully', new UserAuthResource($user));
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('register user is failed');
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->success('user logout successfully');
        } catch (\Exception $ex) {
            return $this->fail('logout is failed' . $ex->getMessage());
        }
    }

}

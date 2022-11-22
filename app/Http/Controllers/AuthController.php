<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        try {
            DB::beginTransaction();
            if (!Auth::guard()->attempt($credentials,true)) {
                return response(['state' => false, 'message' => 'Credenziali non valide'], \Illuminate\Http\Response::HTTP_UNAUTHORIZED);
            }
            $request->session()->regenerate();
            $api_token =  hash('sha256', Str::random(60));
            $user = Auth::user();
            $user['api_token'] = $api_token;
            User::find(Auth::id())->update(['api_token' => $api_token]);
            $accessToken = $user->createToken('authToken');
            DB::commit();
            return response(['state' => true, 'user' => $user, 'csrf_token' => csrf_token(), 'accessToken' => $accessToken->plainTextToken]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['errore' => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function welcome()
    {
        return view('welcome');
    }


    public function registerAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        try {
            $password = Hash::make($request->password);
            $username = $request->username;

            $user = User::create([
                'username' => $username,
                'password' => $password,
                'api_token' => hash('sha256', Str::random(60)),
                'isadmin' => true
            ]);

            return response(['user' => new UserResource($user)]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['errore' => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function logout(Request $request)
    {
        try {
            if (auth()->check()) {
                $request->user()->tokens()->delete();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return response(['message' => "Logout success"]);
            }
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['errore' => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function csrfToken()
    {
        return response(['token' => csrf_token()]);
    }

    public function registerClient(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        try {
            $password = Hash::make($request->password);
            $username = $request->username;

            $user = User::create([
                'username' => $username,
                'password' => $password,
                'api_token' => hash('sha256', Str::random(60)),
                'isadmin' => false
            ]);

            return response(['user' => new UserResource($user)]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['errore' => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

}

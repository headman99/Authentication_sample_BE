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
            $user = User::where("username", $request->username)->first();
            if ($user && $user->pending && $user->isadmin == 0)
                return response(["state" => 2]);
            
            if (!Auth::guard()->attempt($credentials, true)) {
                return response(['state' => 0, 'message' => 'Credenziali non valide'], \Illuminate\Http\Response::HTTP_UNAUTHORIZED);
            }
            $request->session()->regenerate();
            $api_token =  hash('sha256', Str::random(60));
            $user = Auth::user();
            $user['api_token'] = $api_token;
            User::find(Auth::id())->update(['api_token' => $api_token]);
            $accessToken = $user->createToken('authToken');
            DB::commit();
            return response(['state' => 1, 'user' => new UserResource($user), 'csrf_token' => csrf_token(), 'accessToken' => $accessToken->plainTextToken]);
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

            $randomBadge = '';
            do {
                $min = 10000000; // numero minimo
                $max = 99999999; // numero massimo
                $randomNumber = random_int($min, $max); // numero casuale compreso tra $min e $max
                $randomBadge = str_pad($randomNumber, 8, '0', STR_PAD_LEFT); // stringa numerica casuale di lunghezza massima 8
            } while (User::where("badge", $randomBadge)->first() != null);

            $user = User::create([
                'username' => $username,
                'badge' => $randomBadge,
                'password' => $password,
                'api_token' => hash('sha256', Str::random(60)),
                'isadmin' => 1,
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

    public function completeClientRegistration(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:1',
            "pending" => "required|string"
        ]);

        try {

            $password = Hash::make($request->password);
            $username = $request->username;
            $user = User::whereNull("password")->where([
                ["username",$username],
                ["pending",$request->pending]
            ])->first();

            if(!$user)
                return response(["state" => 0,"message"=> "Codice errato"], \Illuminate\Http\Response::HTTP_BAD_REQUEST);

            $user->update(["password" => $password,"pending" => NULL]);

            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(["message"=>"Credenziali non valide",'errore' => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }
}

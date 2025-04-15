<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'max:32',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\d\S]{6,}$/'
                ]
            ],
            [
                'name.required' => 'Lütfen isim alanını doldurunuz.',
                'name.max' => 'İsim en fazla 255 karakter olmalıdır.',
                'email.required' => 'Lütfen email alanını doldurunuz.',
                'email.email' => 'Lütfen geçerli bir email giriniz.',
                'email.unique' => 'Bu email zaten kayıtlı, lütfen başka bir tane giriniz.',
                'password.required' => 'Şifre alanı zorunludur.',
                'password.min' => 'Şifre minimum 6 karakter olmalıdır.',
                'password.max' => 'Şifre maksimum 32 karakter olmalıdır.',
                'password.regex' => 'Şifreniz en az bir küçük harf, bir büyük harf, bir sayı ve bir özel karakter içermelidir.',
            ]
        );
        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = JWTAuth::fromUser($user);

        if (!$token) {
            return response()->json(['error' => 'Token Olusturulurken Bir Hata Olustu.']);
        }

        return response()->json([
            'success' => 'Kullanici Basariyla Olusturuldu.',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only(['email', 'password']);

        try {
            $token = JWTAuth::attempt($credentials);
            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email veya şifre hatalı.',
                ], 401);
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $JWTException) {
            return response()->json([
                'status' => false,
                'message' => 'Token Olustururken Bir Hata Olustu',
                'error' => $JWTException->getMessage()
            ], 500);
        }
        $user = Auth::user();
        return response()->json([
            'status' => true,
            'message' => 'Basariyla Giris Yapildi.',
            'user' => $user,
            'token' => $token
        ]);
    }
    
    public function logout(){
        
        try {
            auth()->guard('api')->logout();
            return response()->json([
                'status' => true,
                'message' => 'Basariyla Cikis Yapildi'
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $jWTException) {
            return response()->json([
                'status' => false,
                'message' => 'Cikis Yapilirken Bir Hata Olustu',
                'error' => $jWTException->getMessage()
            ], 500);
        }
    }
    public function me(){
        return response()->json(Auth::user());
    }
    public function refresh(){
        try {
            $oldToken = JWTAuth::getToken();
            $newToken = $this->createNewtoken(Auth::refresh());
            return response()->json([
                'status' => true,
                'message' => 'Token Basariyla Yenilendi',
                'old_token' => $oldToken,
                'new_Token' => $newToken,
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $tokenInvalidException) {
            return response()->json([
                'status' => false,
                'message' => 'Token Yenilenme sirasinda bir hata olustu.',
                'error' => $tokenInvalidException->getMessage()
            ]);
        }
       
    }

    public function createNewtoken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => Auth::user()
        ]);
    }
}

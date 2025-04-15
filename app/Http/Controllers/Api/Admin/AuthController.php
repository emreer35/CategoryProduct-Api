<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Js;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Pest\Laravel\json;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\d\S]{6,}$/', 'min:6', 'max:32'],
            'role' => ['required', 'in:admin,user'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Kullanici Olusturulurken Hata Olustu'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Kullanici Basariyla Olusturuldu',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'kullanici Bulunamadi',
            ], 404);
        }
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\d\S]{6,}$/', 'min:6', 'max:32'],
            'role' => ['required', 'in:admin,user'],
        ]);

        $user->name = $request->name;
        $user->role = $request->role;
        if($request->filled('email')){
            $user->email = $request->email;
        }
        if($request->filled('password')){
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Kullanici Basarili bir sekilde guncellendi',
            'user' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if(Auth::user()->id === $user->id){
                return response()->json([
                    'status' => false,
                    'message' => 'Kullanici Kendisini Silemez',
                ],403);
            }
            if(!$user){
                return response()->json([
                    'status' => false,
                    'message' => 'Kullanici Bulunamadi',
                ], 500);
            }

            // isAdmin middleware zaten bu islevi yapiyorum 
            if(Auth::user()->role !== 'admin'){
                return response()->json([
                    'status' => false,
                    'message' => 'Kullanicin bu isleme yetkisi yok'
                ],403);
            }

            JWTAuth::invalidate(JWTAuth::getToken($user));

            $user->delete();
            return response()->json([
                'status' => true,
                'message' => 'Kullanici Basariyla Silindi'
            ],204);

            
        }catch(\Tymon\JWTAuth\Exceptions\JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Token Gecersiz kilinirken bir hata olustu.',
                'error' => $e->getMessage()
            ],500);
        }
         catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => 'Kullanici Silinirken Bir Hata Olustu',
                'error' => $exception->getMessage()
            ],500);
        }
    }
}

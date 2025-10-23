<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/register",
     *   summary="Register user baru",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name","email","password","password_confirmation"},
     *       @OA\Property(property="name", type="string", example="Jimmy"),
     *       @OA\Property(property="email", type="string", format="email", example="jimmy@example.com"),
     *       @OA\Property(property="password", type="string", minLength=6, example="secret123"),
     *       @OA\Property(property="password_confirmation", type="string", example="secret123")
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Register success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Register success"),
     *       @OA\Property(property="user", type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Jimmy"),
     *         @OA\Property(property="email", type="string", example="jimmy@example.com")
     *       ),
     *       @OA\Property(property="token", type="string", example="1|abcDEF...token")
     *     )
     *   ),
     *   @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function register(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => 'Register success',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * @OA\Post(
     *   path="/api/login",
     *   summary="Login user dan dapatkan token",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="jimmy@example.com"),
     *       @OA\Property(property="password", type="string", example="secret123")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Login success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Login success"),
     *       @OA\Property(property="user", type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Jimmy"),
     *         @OA\Property(property="email", type="string", example="jimmy@example.com")
     *       ),
     *       @OA\Property(property="token", type="string", example="1|abcDEF...token")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Invalid credentials"),
     *   @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function login(Request $r)
    {
        $r->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $r->email)->first();

        if (!$user || !Hash::check($r->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'user' => $user,
            'token' => $token,
        ]);
    }
}

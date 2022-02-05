<?php


namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use App\Utils\StatusCode;
use Carbon\Carbon;
use Hash;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /**
     * @throws AuthenticationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if($request->hasHeader('Authorization')) {
            return response()->json(['message' => 'Authorization token was in request header'],
                                    StatusCode::ALREADY_REPORTED
            );
        }

        $request->validated();
        $tokenResult = AuthServiceProvider::loginLogic($request->credentials(), request('remember_me', false));
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at->toDateTimeString()
        ], StatusCode::ACCEPTED);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $request->validated();
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);
        return response()->json([], StatusCode::CREATED);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json([], StatusCode::ACCEPTED);
    }

    public function profile(): JsonResponse
    {
        return response()->json(UserResource::make(Auth::user()));
    }
}

<?php

namespace App\Repositories\Auth;

use App\Http\Resources\Client\ClientUserResource;
use App\Repositories\Client\ClientRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class LoginRepository
{

    protected ClientRepository $client;
    public function __construct(ClientRepository $client){
        $this->client = $client;
    }

    public function loginWithToken(
        $request,
        String $webGuard,
        String $apiGuard,
        $queryBuilder
    ): array
    {
        if(Auth::guard($webGuard)->attempt([
            'email' => trim($request->email),
            'password' => trim($request->password),
        ])){
            // get Session
            $user = Auth::user();

            if(!$user->system_access){
                return [
                    'success' => false,
                    'errors' => [
                        'unauthorised' => 'Unauthorised.',
                    ],
                    'error_code' => 401,
                ];
            }

            // Delete already existing tokens for user
            PersonalAccessToken::where('name', $request->email)->delete();
            // Create new token
            $token = $user->createToken($request->email, [$apiGuard])->plainTextToken;
            // Last login
            $queryBuilder->where('email', $request->email)->update([
                'last_login' => Carbon::now()->format('Y-m-d h:i:s'),
            ]);

            return [
                'success' => true,
                'user' => $user,
                'token' => $token,
            ];
        }

        return [
            'success' => false,
            'errors' => [
                'unauthorised' => 'Unauthorised.',
            ],
            'error_code' => 401,
        ];
    }

    public function authenticateUserWithToken($token): array
    {
        $user = PersonalAccessToken::findToken($token)->tokenable;
        return [
            'success' => true,
            'user' => new ClientUserResource($user),
        ];
    }

    public function LogoutAndDeleteAccessTokens($guard, $token): array
    {
        // Delete access tokens with email and logout of this guard
        $data = PersonalAccessToken::findToken($token)->tokenable;
        PersonalAccessToken::where('name', $data->email)->delete();

        Auth::guard($guard)->logout();
        return [
            'success' => true,
            'message' => 'Logged Out',
        ];
    }

}

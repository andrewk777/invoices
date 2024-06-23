<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Client\ClientUserResource;
use App\Repositories\Auth\LoginRepository;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Eloquent\Client\ClientRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    protected LoginRepository $login;
    protected ClientRepository $client;
    public function __construct(LoginRepository $login, ClientRepository $client){
        $this->login = $login;
        $this->client = $client;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->login->loginWithToken(
                $request,
                'web',
                'api',
                $this->client->clientUser()
            );

            return response()->json([
                'success' => $data['success'],
                'user' => isset($data['user']) ? new ClientUserResource($data['user']) : null,
                'token' => $data['token'] ?? null,
                'errors' => $data['errors'] ?? null,

            ], $data['success'] === true ? Response::HTTP_OK : Response::HTTP_UNAUTHORIZED);

        } catch (\Exception $e) {
            return BaseRepository::tryCatchException($e);
        }
    }

}

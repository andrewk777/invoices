<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\StoreClientUserRequest;
use App\Http\Requests\Client\UpdateClientUserRequest;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Client\ClientUserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientUserController extends Controller
{
    protected ClientUserRepository $clientUser;
    public function __construct(ClientUserRepository $clientUser)
    {
        $this->clientUser = $clientUser;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($client_hash): JsonResponse
    {
        try {
            $response = $this->clientUser->getClientsFromUser($client_hash);
            return response()->json($response);

        } catch (\Exception $e) {
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientUserRequest $request, $client_hash): JsonResponse
    {
        try {
            $response = $this->clientUser->addClientUser($request->all(), $client_hash);
            return response()->json($response);

        } catch (\Exception $e) {
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(UpdateClientUserRequest $request, $client_hash, $client_user_hash): JsonResponse
    {
        try {
            $response = $this->clientUser->updateClientUser($request->all(), $client_hash, $client_user_hash);
            return response()->json($response);

        } catch (\Exception $e) {
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): void
    {
        //
    }

    public function access($client_user_hash): JsonResponse
    {
        try {
            $response = $this->clientUser->userLoginAccess($client_user_hash);
            return response()->json($response);

        } catch (\Exception $e) {
            return BaseRepository::tryCatchException($e);
        }
    }
}

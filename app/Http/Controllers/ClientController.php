<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\Client\ClientResource;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Client\ClientRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private ClientRepository $client;
    public function __construct(ClientRepository $client)
    {
        $this->client = $client;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $data = $this->client->client()->with('creditCards:hash,client_id,cc_provider')
                ->orderBy('id', 'desc')->get();
            return response()->json([
                'success' => true,
                'clients' => ClientResource::collection($data),
                'total' => $data->count(),
            ]);

        }catch (\Exception $e) {
            return BaseRepository::tryCatchException($e);
        }
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $data = $this->client->storeClient($request);
            return response()->json($data, $data['status_code'] ?? 200);
        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    public function show($hash): JsonResponse
    {
        try {
            $data = $this->client->client()
                ->with('users', 'creditCards')
                ->where('hash', $hash)->first();
            return response()->json([
                'success' => true,
                'client' => new ClientResource($data),
            ]);
        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    public function update(UpdateClientRequest $request, $hash): JsonResponse
    {
        try {
            $data = $this->client->updateClient($request, $hash);
            return response()->json($data, $data['status_code'] ?? 200);
        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    public function delete($item_id): JsonResponse
    {
        try {
            $data = $this->client->deleteClient($item_id);
            return response()->json($data, $data['status_code'] ?? 200);
        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }
}

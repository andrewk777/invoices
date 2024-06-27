<?php

namespace App\Repositories\Client;

use App\Http\Resources\Client\ClientResource;
use App\Models\Client;
use App\Models\User;
use App\Repositories\Base\BaseRepository;

/**
 * Class ClientRepository.
 */
class ClientRepository
{
    public function client(): Client
    {
        return new Client();
    }

    public function clientUser(): User
    {
        return new User();
    }

    public function storeClient($request): array
    {
        $inputs = $request->all();
        $inputs['hash'] = BaseRepository::randomCharacters(50, '0123456789ABSDEFGHIJKLMNOPQRSTUVWXYZ');
        $client = $this->client()->create($inputs);

        return [
            'success' => true,
            'client' => new ClientResource($client)
        ];
    }

    public function updateClient($request, $hash): array
    {
        $client = $this->client()->where('hash', $hash)->first();
        $inputs = $request->all();
        $client->update($inputs);

        return [
            'success' => true,
            'client' => new ClientResource($client)
        ];
    }

    public function deleteClient($hash): array
    {
        $client = $this->client()->where('hash', $hash)->first();
        // Check if the achievement exists
        if (!$client) {
            return [
                'success' => false,
                'error_message' => 'Client not found'
            ];
        }

        // Delete Credit cards
        if($client->creditCards()?->count() > 0){
            $client->creditCards()->delete();
        }

        $client->delete();

        return [
            'success' => true,
            'message' => 'Client deleted successfully'
        ];
    }

}

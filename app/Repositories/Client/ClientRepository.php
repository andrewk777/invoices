<?php

namespace App\Repositories\Client;

use App\Http\Resources\Client\ClientResource;
use App\Models\Client;
use App\Models\User;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\Session;

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

    public function searchClients($request): array
    {
        $search = $request->all()['query'];
        Session::forget(['search_inputs', 'search_values']);

        $clients = $this->client()->with('company')->select(

            'my_companies.id as my_company_id',
            'my_companies.name as my_company_name',
            'clients.*'

        )->leftjoin('my_companies', 'my_companies.id', '=', 'clients.my_company_id')
            ->where(function($query) use ($search){
                $query->when(!empty($search), static function($q) use($search){
                    $q->where('clients.company_name', 'like' , '%'. $search .'%')
                        ->orWhere('clients.company_email', 'like' , '%'. $search .'%')
                        ->orWhere('clients.company_address', 'like' , '%'. $search .'%')
                        ->orWhere('clients.company_phone', 'like' , '%'. $search .'%')
                        ->orWhere('clients.main_contact_first_name', 'like' , '%'. $search .'%')
                        ->orWhere('clients.main_contact_last_name', 'like' , '%'. $search .'%');
                });

            })->latest()->get();

        // if a result exists return results, else return empty array
        if($clients->count() > 0){
            return [
                'success' => true,
                'clients' => ClientResource::collection($clients),
                'total' => $clients->count(),
                'search_values' => Session::get('search_values')
            ];
        }

        return [
            'success' => true,
            'clients' => [],
            'total' => 0,
            'search_values' => Session::get('search_values')
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

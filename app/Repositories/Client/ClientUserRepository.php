<?php

namespace App\Repositories\Client;

use App\Models\Client;
use App\Models\ClientUser;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\Hash;

class ClientUserRepository
{
    public function clientUser(): ClientUser
    {
        return new ClientUser();
    }

    public function client(): Client
    {
        return new Client();
    }

    public function addClientUser($request, $clientHash): array
    {
        $input = $request->all();
        $client = $this->client()->where('hash', $clientHash)->first();

        $user = $this->clientUser()->create([
            'hash' => BaseRepository::randomCharacters(30, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),
            'client_id' => $client->id,
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'system_access' => $input['system_access'],
        ]);

        return [
            'success' => true,
            'client_user' => $user,
        ];
    }

    public function getClientsFromUser($clientHash): array
    {
        $client = $this->client()->where('hash', $clientHash)->first();
        $users = $this->clientUser()->where('client_id', $client->id)->get();

        return [
            'success' => true,
            'client_users' => $users,
        ];
    }

    public function blockUserLoginAccess($clientUserHash): array
    {
        $user = $this->clientUser()->where('hash', $clientUserHash)->first();
        $user->system_access === 1 ? $user->system_access = 0 : $user->system_access = 1;
        $user->save();

        return [
            'success' => true,
            'client_user' => $user,
            'access' => $user->system_access === 1 ? 'User unblocked' : 'User blocked'
        ];
    }

}

<?php

namespace App\Repositories\Client;

use App\Http\Resources\User\UserResource;
use App\Models\Client;
use App\Models\User;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\Hash;

class ClientUserRepository
{
    public function clientUser(): User
    {
        return new User();
    }

    public function client(): Client
    {
        return new Client();
    }

    public function addClientUser($request, $clientHash): array
    {
        $input = $request;
        $client = $this->client()->where('hash', $clientHash)->first();

        $firstName = explode(' ', $input['name'])[0];
        $lastName = explode(' ', $input['name'])[1] ?? '';

        $user = $this->clientUser()->create([
            'hash' => BaseRepository::randomCharacters(30, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),
            'client_id' => $client->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'system_access' => $input['system_access'],
            'role' => 'client-user'
        ]);

        return [
            'success' => true,
            'client_user' => new UserResource($user),
        ];
    }

    public function updateClientUser($request, $client_hash, $client_user_hash): array
    {
        $user = $this->clientUser()->with('client:id,hash')
            ->whereHas('client', function ($query) use ($client_hash) {
            $query->where('hash', $client_hash);
        })->where('hash', $client_user_hash)->firstOrFail();

        if(!$user) {
            return [
                'success' => false,
                'errors' => ['unauthorised' => ['You are not authorised to update this user.']]
            ];
        }

        $input = $request;

        $firstName = explode(' ', $input['name'])[0];
        $lastName = explode(' ', $input['name'])[1] ?? '';

        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->email = $input['email'];

        if(!empty($input['password'])) {
            $user->password = Hash::make($input['password']);
        }
        $user->system_access = $input['system_access'];
        $user->save();

        return [
            'success' => true,
            'client_user' => new UserResource($user),
            'message' => $user->name.' updated successfully.'
        ];
    }

    public function getClientsFromUser($clientHash): array
    {
        $client = $this->client()->where('hash', $clientHash)->first();
        $users = $this->clientUser()->where('client_id', $client->id)->get();

        return [
            'success' => true,
            'client_users' => UserResource::collection($users),
        ];
    }

    public function userLoginAccess($clientUserHash): array
    {
        $user = $this->clientUser()->where('hash', $clientUserHash)->first();
        $user->system_access === 1 ? $user->system_access = 0 : $user->system_access = 1;
        $user->save();

        $clientUsers = $this->clientUser()->where('client_id', $user->client_id)->get();

        return [
            'success' => true,
            'client_user' => $user,
            'client_users' => $clientUsers,
            'access' => $user->system_access === 1 ? $user->name.' unblocked' : $user->name.' blocked'
        ];
    }

}

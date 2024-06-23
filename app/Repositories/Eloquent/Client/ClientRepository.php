<?php

namespace App\Repositories\Eloquent\Client;

use App\Models\Client;
use App\Models\User;

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

}

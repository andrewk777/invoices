<?php

namespace App\Repositories\Subscription;

use App\Models\Subscription;

class SubscriptionRepository
{
    public function subscription(): Subscription
    {
        return new Subscription();
    }

    public function storeSubscription(){

    }
}

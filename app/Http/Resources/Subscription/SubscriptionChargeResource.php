<?php

namespace App\Http\Resources\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionChargeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hash' => $this->hash,
            'subscription_id' => $this->subscription_id,
            'description' => $this->description,
            'qty' => $this->qty,
            'rate' => $this->rate,
            'tax' => $this->tax === 1 ? 'HST' : 'None',
        ];
    }
}

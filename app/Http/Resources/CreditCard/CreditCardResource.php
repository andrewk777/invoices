<?php

namespace App\Http\Resources\CreditCard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditCardResource extends JsonResource
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
            //'hash' => $this->hash,
//            'client_id' => $this->client_id,
            'cc_provider' => $this->cc_provider,
//            'cc_number' => $this->cc_number,
            'cc_exp_month' => $this->cc_exp_year,
            'cc_exp_year' => $this->cc_exp_year,
            'cc_last_4_digits' => substr($this->cc_number, -4),
//            'cc_provider_customer_id' => $this->cc_provider_customer_id,
//            'cc_provider_client_id' => $this->cc_provider_client_id,
            'cc_currencies' => $this->cc_currencies,
        ];
    }
}

<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\CreditCard\CreditCardResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientMinResource extends JsonResource
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
            'company_name' => $this->company_name . (count($this->creditCards) > 0 ? ' (cc)' : ''),
            'company_address' => $this->company_address,
            'company_phone' => $this->company_phone,
            'company_email' => $this->company_email,
            'default_credit_card' => $this->defaultCreditCard ?? '',
            'default_credit_card_id' => $this->default_credit_card_id,
            'credit_cards' => $this->creditCards && count($this->creditCards) > 0 ? CreditCardResource::collection($this->creditCards) : [],
        ];
    }
}

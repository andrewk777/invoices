<?php

namespace App\Http\Resources\Subscription;

use App\Http\Resources\Client\ClientResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'name' => $this->name,

            'my_company_id' => $this->my_company_id,
            'client_id' => $this->client_id,

            'company' => $this->company ?? null,
            'client' => $this->client ? new ClientResource($this->client) : null,

            'charges' => $this->charges && count($this->charges) > 0 ? SubscriptionChargeResource::collection($this->charges) : [],

            'tags' => $this->tags,
            'currency' => $this->currency,
            'credit_card_id' => $this->credit_card_id,
            'next_charge_date' => Carbon::parse($this->next_charge_date)->format('F j, Y'),
            'due_in_days' => $this->due_in_days,
            'frequency_day' => $this->frequency_day,
            'frequency_month' => $this->frequency_month,
            'can_pay_with_cc' => $this->can_pay_with_cc,
            'starting_date' => Carbon::parse($this->starting_date)->format('F j, Y'),
            'expiration_date' => Carbon::parse($this->expiration_date)->format('F j, Y'),
            'charge_cc' => $this->charge_cc,
            'email_invoice' => $this->email_invoice,

            'subtotal' => $this->subtotal,
            'taxes' => $this->taxes,
            'total' => $this->total,
        ];
    }
}

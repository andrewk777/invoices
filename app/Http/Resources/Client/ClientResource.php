<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'my_company_id' => $this->my_company_id,

            'company_name' => $this->company_name,
            'company_address' => $this->company_address,
            'company_phone' => $this->company_phone,
            'company_email' => $this->company_email,

            'main_contact_first_name' => $this->main_contact_first_name,
            'main_contact_last_name' => $this->main_contact_last_name,
            'main_contact_phone' => $this->main_contact_phone,
            'main_contact_email' => $this->main_contact_email,

            'ap_first_name' => $this->ap_first_name,
            'ap_last_name' => $this->ap_last_name,
            'ap_phone' => $this->ap_phone,
            'ap_email' => $this->ap_email,

            'credit_cards' => $this->creditCards && count($this->creditCards) > 0 ? $this->creditCards : [],
            'users' => $this->users && count($this->users) > 0 ? $this->users : [],

            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}

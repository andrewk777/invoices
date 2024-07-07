<?php

namespace App\Http\Resources\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
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
            'invoice_id' => $this->invoice_id,
            'description' => $this->description,
            'qty' => $this->qty,
            'rate' => $this->rate,
            'tax' => $this->tax,
        ];
    }
}

<?php

namespace App\Http\Resources\Invoice;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'invoice_num' => $this->invoice_num,
            'invoice_type' => $this->invoice_type,
            'status' => $this->displayStatus($this->status),
            'currency' => $this->currency,
            'invoice_date' => Carbon::parse($this->invoice_date)->format('F j, Y'),
            'invoice_due' => Carbon::parse($this->invoice_due)->format('F j, Y'),
            'na' => $this->na,
            'can_pay_with_cc' => $this->can_pay_with_cc,
            'subtotal' => $this->subtotal,
            'taxes' => $this->taxes,
            'total' => $this->total,
            'total_paid' => $this->total_paid,
            'balance' => $this->balance,

            'my_company_id' => $this->my_company_id,
            'company' => $this->company ?? '',
            'client_id' => $this->client_id,
            'client' => $this->client ?? '',

            'items' => $this->items ? InvoiceItemResource::collection($this->items) : [],
            'payments' => $this->payments ? InvoicePaymentResource::collection($this->payments) : [],
        ];
    }

    private function displayStatus($status): string
    {
       $output = '';
       if($status === 'draft') {
           $output = 'Draft';
       } elseif($status === 'approved') {
           $output = 'Approved';
       } elseif($status === 'sent') {
           $output = 'Sent';
       } elseif($status === 'partially_paid') {
           $output = 'Partially Paid';
       } elseif($status === 'paid') {
           $output = 'Paid';
       }
       return $output;
    }

}

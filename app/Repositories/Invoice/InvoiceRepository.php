<?php

namespace App\Repositories\Invoice;

use App\Models\Invoice;

class InvoiceRepository
{
    public function invoice(): Invoice
    {
        return new Invoice();
    }
}

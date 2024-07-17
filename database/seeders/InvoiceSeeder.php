<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Invoice::class::factory(10)->create()->each(function ($invoice) {
            $invoice->items()->saveMany(InvoiceItem::factory(2)->create());
            $invoice->items()->saveMany(InvoicePayment::factory(2)->create());
            $invoice->save();
        });
    }
}

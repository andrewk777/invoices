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

        //select last invoices created
        $invoice = Invoice::orderBy('id', 'desc')->first();
        //delete all its items and payments
        $invoice->items()->delete();
        $invoice->payments()->delete();
        $invoice->items()->create(
            [
                'description' => 'Item 1',
                'qty' => 3,
                'rate' => 100,
                'tax' => 1,
            ]);
        $invoice->items()->create(
            [
                'description' => 'Hosting and maintenance',
                'qty' => 5,
                'rate' => 25,
                'tax' => 0,
            ]);
        $invoice->update(['invoice_num' => '0001','status' => 'draft']);
    }
}

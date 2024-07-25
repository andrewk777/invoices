<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Invoice\InvoiceRepository;

class generalTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ir = new InvoiceRepository();

        $testData = [

        ];

        $ir->updateInvoice($testData);
    }
}

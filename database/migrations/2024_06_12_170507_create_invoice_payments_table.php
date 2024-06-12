<?php
// database/migrations/xxxx_xx_xx_create_invoice_payments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 50)->unique();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->date('date')->nullable();
            $table->enum('type', ['cheque', 'credit_card', 'cash', 'eft', 'crypto'])->nullable();
            $table->text('note')->nullable();
            $table->timestamps(0); // no updated_at column

            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_payments');
    }
}
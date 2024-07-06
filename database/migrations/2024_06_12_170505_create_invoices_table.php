<?php
// database/migrations/xxxx_xx_xx_create_invoices_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 50)->unique();
            $table->unsignedBigInteger('my_company_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('invoice_num')->nullable();
            $table->enum('invoice_type', ['standard', 'credit_memo'])->nullable();
            $table->enum('status', ['draft', 'approved', 'sent', 'partially_paid', 'paid'])->nullable();
            $table->enum('currency', ['USD', 'CAD'])->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('invoice_due')->nullable();
            $table->boolean('na')->default(false)->nullable();
            $table->boolean('can_pay_with_cc')->default(false)->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('taxes', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->decimal('total_paid', 10, 2)->default(0)->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('my_company_id')->references('id')->on('my_companies');
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}

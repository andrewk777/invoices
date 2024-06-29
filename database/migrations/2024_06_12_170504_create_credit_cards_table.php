<?php
// database/migrations/xxxx_xx_xx_create_credit_cards_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditCardsTable extends Migration
{
    public function up()
    {
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 50)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('stripe_id')->nullable();
            $table->enum('cc_currencies', ['CAD', 'USD'])->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('clients');
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_cards');
    }
}
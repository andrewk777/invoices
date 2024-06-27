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
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('cc_provider')->nullable();
            $table->string('cc_number')->nullable();
            $table->string('cc_provider_customer_id')->nullable();
            $table->string('cc_provider_card_id')->nullable();
            $table->enum('cc_currencies', ['CAD', 'USD'])->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign('subscriptions_credit_card_id_foreign');
        });
        Schema::dropIfExists('credit_cards');
    }
}

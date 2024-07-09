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
            $table->string('cc_exp_month')->nullable();
            $table->string('cc_exp_year')->nullable();
            $table->string('cc_provider_customer_id')->nullable();
            $table->string('cc_provider_card_id')->nullable();
            $table->enum('cc_currencies', ['CAD', 'USD'])->nullable();
            $table->enum('cc_type', ['VISA', 'MASTER CARD', 'AMEX', 'Other'])->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    public function down()
    {
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropForeign(['credit_card_id']);
            });
        }

        Schema::dropIfExists('credit_cards');
    }
}


<?php
// database/migrations/xxxx_xx_xx_create_subscriptions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 50)->unique();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('my_company_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->json('tags')->nullable();
            $table->enum('currency', ['CAD', 'USD'])->nullable();
            $table->unsignedBigInteger('credit_card_id')->nullable();
            $table->date('next_charge_date')->nullable();
            $table->tinyInteger('due_in_days')->nullable();
            $table->tinyInteger('frequency_day')->nullable();
            $table->tinyInteger('frequency_month')->nullable();
            $table->boolean('can_pay_with_cc')->default(false);
            $table->date('starting_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->boolean('charge_cc')->default(false);
            $table->boolean('email_invoice')->default(false);
            $table->unsignedBigInteger('email_template_id')->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('taxes', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();

//            $table->foreign('my_company_id')->references('id')->on('my_companies');
//            $table->foreign('client_id')->references('id')->on('clients');
//            $table->foreign('credit_card_id')->references('id')->on('credit_cards');
//            $table->foreign('email_template_id')->references('id')->on('email_templates');
        });
    }

    public function down()
    {
        if (Schema::hasTable('subscription_items')) {
            Schema::table('subscription_items', function (Blueprint $table) {
                $table->dropForeign(['subscription_id']);
            });
        }

        Schema::dropIfExists('subscriptions');
    }
}


<?php

// database/migrations/xxxx_xx_xx_create_subscription_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionItemsTable extends Migration
{
    public function up()
    {
        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 50)->unique();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->string('description')->nullable();
            $table->integer('qty')->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->boolean('tax')->default(false);

            $table->foreign('subscription_id')->references('id')->on('subscriptions');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_items');
    }
}
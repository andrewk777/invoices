<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 50)->unique();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_logs');
    }
};

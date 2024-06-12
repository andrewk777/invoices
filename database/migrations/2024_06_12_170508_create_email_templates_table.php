<?php
// database/migrations/xxxx_xx_xx_create_email_templates_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 50)->unique();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('name')->nullable();
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->json('recipients')->nullable();

            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_templates');
    }
}
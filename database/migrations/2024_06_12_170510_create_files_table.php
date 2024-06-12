
<?php

// database/migrations/xxxx_xx_xx_create_files_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 50)->unique();
            $table->string('name')->nullable();
            $table->string('path')->nullable();
            $table->string('ext')->nullable();
            $table->string('mime')->nullable();
            $table->bigInteger('size')->nullable();
            $table->morphs('attachment');
            $table->boolean('is_saved')->default(true);
            $table->unsignedBigInteger('user_id_created')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id_created')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
}
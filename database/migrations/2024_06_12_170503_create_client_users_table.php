<?php
// database/migrations/xxxx_xx_xx_create_client_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientUsersTable extends Migration
{
    public function up()
    {
        Schema::create('client_users', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 50)->unique();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->boolean('system_access')->default(false);
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_users');
    }
}

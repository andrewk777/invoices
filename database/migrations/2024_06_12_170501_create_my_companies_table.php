<?php
// database/migrations/xxxx_xx_xx_create_my_companies_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('my_companies', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 50)->unique();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {

        Schema::dropIfExists('my_companies');
    }
}

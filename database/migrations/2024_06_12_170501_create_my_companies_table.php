<?php

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

            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        // Drop foreign key
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'my_company_id')) {
                $table->dropForeign(['my_company_id']);
            }
        });
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'my_company_id')) {
                $table->dropForeign(['my_company_id']);
            }
        });
        Schema::dropIfExists('my_companies');
    }
}

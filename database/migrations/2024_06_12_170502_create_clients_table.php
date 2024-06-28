<?php
// database/migrations/xxxx_xx_xx_create_clients_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 50)->unique();
            $table->foreignId('my_company_id')->nullable()->constrained('my_companies');
            $table->string('company_name')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('main_contact_first_name')->nullable();
            $table->string('main_contact_last_name')->nullable();
            $table->string('main_contact_phone')->nullable();
            $table->string('main_contact_email')->nullable();
            $table->string('ap_first_name')->nullable();
            $table->string('ap_last_name')->nullable();
            $table->string('ap_phone')->nullable();
            $table->string('ap_email')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
}

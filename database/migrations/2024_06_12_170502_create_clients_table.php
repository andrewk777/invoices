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
            $table->unsignedBigInteger('my_company_id')->nullable();
            $table->unsignedBigInteger('default_credit_card_id')->nullable();
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

            $table->foreign('my_company_id')->references('id')->on('my_companies');
            //$table->foreign('default_credit_card_id')->references('id')->on('credit_cards');
        });
    }

    public function down()
    {
//        if (Schema::hasTable('client_users')) {
//            Schema::table('client_users', function (Blueprint $table) {
//                $table->dropForeign(['client_id']);
//            });
//        }
//
//        if (Schema::hasTable('credit_cards')) {
//            Schema::table('credit_cards', function (Blueprint $table) {
//                $table->dropForeign(['client_id']);
//            });
//        }

//        Schema::table('clients', function (Blueprint $table) {
//            $table->dropForeign('clients_my_company_id_foreign');
//            $table->dropColumn('my_company_id');
//        });


        Schema::dropIfExists('clients');
    }
}

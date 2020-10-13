<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');

            $table->enum('type', ['supplier', 'customer', 'biller', 'both']);

            $table->string('name');
            $table->string('company')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('identity')->nullable();

            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();


            $table->string('invoice_footer')->nullable();
            $table->string('logo')->nullable();

            $table->integer('created_by')->unsigned();
            $table->boolean('is_default')->default(0);
            $table->softDeletes();
            
            $table->timestamps();
            $table->index('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}

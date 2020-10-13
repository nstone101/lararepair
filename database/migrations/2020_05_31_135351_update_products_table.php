<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('unit_id')->unsigned()->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });


        Schema::table('sale_items', function (Blueprint $table) {
            $table->integer('product_unit')->unsigned()->nullable();
            $table->foreign('product_unit')->references('id')->on('units')->onDelete('cascade');
        });

        Schema::table('costing', function (Blueprint $table) {
            $table->integer('sale_id')->unsigned()->nullable();
        });

          

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

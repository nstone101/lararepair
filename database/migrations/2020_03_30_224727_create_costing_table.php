<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCostingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('costing', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('repair_id')->nullable()->unsigned();
            $table->foreign('repair_id')->references('id')->on('repairs');

            $table->integer('sale_item_id')->nullable()->unsigned();
            $table->foreign('sale_item_id')->references('id')->on('sale_items');

            $table->integer('product_id')->nullable()->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->string('code');
            $table->string('name');
            $table->decimal('cost', 8, 2);
            $table->decimal('quantity', 8, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('costing');
    }
}

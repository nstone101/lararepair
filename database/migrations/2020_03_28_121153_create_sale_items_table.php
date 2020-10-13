<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('repair_id')->unsigned()->nullable();
            $table->foreign('repair_id')->references('id')->on('repairs')->onDelete('cascade');

            $table->integer('sale_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products');

            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_type')->nullable();
            $table->decimal('net_unit_price', 8, 2)->default(0);    
            $table->decimal('unit_price', 8, 2)->default(0);    
            $table->decimal('quantity', 8, 2)->default(0);    
            $table->string('item_tax')->default(0);    
            
            $table->integer('taxrate_id')->unsigned()->nullable();
            $table->foreign('taxrate_id')->references('id')->on('tax_rates');

            $table->decimal('tax', 8, 2)->default(0);    
            $table->string('discount')->nullable();
            $table->decimal('item_discount', 8, 2)->default(0);    
            $table->decimal('subtotal', 8, 2)->default(0);    
            $table->decimal('purchase_price_gross', 8, 2)->default(0);    
            $table->string('serial_number')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('sale_items');
    }
}

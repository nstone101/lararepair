<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuspendedBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suspended_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('companies');
            $table->string('customer')->nullable();
            $table->string('count')->nullable();
            $table->string('order_discount_id', 10)->nullable();    
            $table->integer('order_tax_id')->nullable()->unsigned();
            $table->foreign('order_tax_id')->references('id')->on('tax_rates');
            $table->decimal('total', 8, 2)->default(0);    
            $table->integer('biller_id')->unsigned();
            $table->foreign('biller_id')->references('id')->on('companies');
            $table->string('biller')->nullable();
            $table->integer('created_by')->unsigned();
            $table->string('suspend_note')->nullable();
            $table->timestamps();
        });

        Schema::create('suspended_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('suspend_id')->unsigned()->nullable();

            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_type')->nullable();
            $table->string('product_unit')->nullable();

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
        Schema::dropIfExists('suspended_bills');
        Schema::dropIfExists('suspended_items');
    }
}

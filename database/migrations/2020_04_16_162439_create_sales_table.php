<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('companies');
            $table->string('customer')->nullable();
            $table->string('reference_no')->nullable();

            $table->integer('total_items')->default(0);


            $table->integer('biller_id')->unsigned();
            $table->foreign('biller_id')->references('id')->on('companies');
            $table->string('biller')->nullable();

            $table->integer('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->string('store')->nullable();
            $table->string('note')->nullable();
            $table->string('staff_note')->nullable();

            $table->decimal('total', 8, 2)->default(0);    
            $table->decimal('product_discount', 8, 2)->default(0);    
            $table->string('order_discount_id', 10)->nullable();    
            $table->decimal('total_discount', 8, 2)->default(0);    
            $table->decimal('order_discount', 8, 2)->default(0);    
            $table->decimal('product_tax', 8, 2)->default(0);    

            $table->integer('order_tax_id')->nullable()->unsigned();
            $table->foreign('order_tax_id')->references('id')->on('tax_rates');
            $table->decimal('order_tax', 8, 2)->default(0);    

            $table->decimal('total_tax', 8, 2)->default(0);    


            $table->decimal('shipping', 8, 2)->default(0);    
            $table->decimal('grand_total', 8, 2)->default(0);    


            $table->string('sale_status', 10)->nullable();    
            $table->string('payment_status', 10)->nullable();    
            $table->smallInteger('payment_term')->default(0);    
            

            $table->date('due_date')->nullable(); 

            $table->integer('created_by')->unsigned();

            $table->integer('updated_by')->unsigned()->nullable();



            $table->smallInteger('totalitems')->default(0);


            $table->boolean('pos')->default(0);

            $table->decimal('paid', 8, 2)->default(0);    

            $table->string('suspend_note')->nullable();    

            $table->string('hash')->nullable();    
            
            // $table->foreign('created_by')->references('id')->on('users');
            // $table->foreign('updated_by')->references('id')->on('users');

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
        Schema::dropIfExists('sales');
    }
}

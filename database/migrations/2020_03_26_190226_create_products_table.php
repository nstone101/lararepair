<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('code');
            $table->string('image')->nullable();
            $table->enum('barcode_symbology', ['C39', 'C128', 'EAN-13', 'EAN-8', 'UPC-A', 'UPC-E', 'ITF-14']);


            $table->integer('model_id')->unsigned()->nullable();

            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->integer('subcategory_id')->unsigned()->nullable();
            $table->foreign('subcategory_id')->references('id')->on('categories')->onDelete('cascade');

            $table->integer('supplier_id')->unsigned()->nullable();
            $table->foreign('supplier_id')->references('id')->on('companies')->onDelete('cascade');


            $table->decimal('price_net', 8, 2)->default(0);    
            $table->integer('tax_id')->unsigned()->nullable();
            $table->foreign('tax_id')->references('id')->on('tax_rates');
            $table->decimal('price_gross', 8, 2)->default(0);  

            $table->boolean('service')->nullable();

            $table->decimal('purchase_price_net', 8, 2)->default(0);    
            $table->integer('purchase_tax_id')->unsigned()->nullable();
            $table->foreign('purchase_tax_id')->references('id')->on('tax_rates');
            $table->decimal('purchase_price_gross', 8, 2)->default(0);    

            $table->integer('alert_quantity')->default(0);
            $table->decimal('quantity')->default(0);

            $table->string('description')->nullable();
            $table->integer('created_by')->unsigned();
            
            $table->timestamps();

            //Indexing
            $table->index('name');
            $table->index('created_by');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}

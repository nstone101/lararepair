<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('companies');

            $table->string('customer')->nullable();

            $table->string('category')->nullable();
            $table->integer('assigned_to')->nullable();

            $table->string('manufacturer');
            $table->integer('manufacturer_id')->nullable();

            $table->string('model');
            $table->integer('model_id')->nullable();

            $table->string('defect')->nullable();
            $table->decimal('service_charges', 8, 2)->default(0);    

            $table->timestamp('expected_close_date')->nullable();  
            $table->string('has_warranty')->nullable();
            $table->string('warranty_period')->nullable();

            $table->integer('taxrate_id')->nullable()->unsigned();
            $table->foreign('taxrate_id')->references('id')->on('tax_rates');
            
            $table->decimal('order_tax', 8, 2)->default(0);    
            $table->decimal('product_tax', 8, 2)->default(0);    
            $table->decimal('total_tax', 8, 2)->default(0);    

            $table->decimal('product_discount', 8, 2)->default(0);    
            $table->decimal('order_discount', 8, 2)->default(0);    
            $table->decimal('total_discount', 8, 2)->default(0);    

            $table->decimal('total', 8, 2)->default(0);    
            $table->decimal('grand_total', 8, 2)->default(0);    
            
            $table->decimal('paid', 8, 2)->default(0);    

            $table->integer('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('statuses');

            $table->string('payment_status')->nullable();
            $table->string('comments')->nullable();
            $table->string('diagnostics')->nullable();

            $table->string('code')->nullable();

            $table->integer('send_email')->default(0);
            $table->integer('send_sms')->default(0);

            $table->string('intake_signature')->nullable();
            $table->string('pattern')->nullable();
            $table->string('pin')->nullable();

            $table->string('serial_number')->nullable();
            $table->string('closed_at')->nullable();

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->longText('repair_toggles')->nullable();

            
            $table->softDeletes();
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
        Schema::dropIfExists('repairs');
    }
}

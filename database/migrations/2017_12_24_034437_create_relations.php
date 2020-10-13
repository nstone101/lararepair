<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('backups', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

     
        Schema::table('activity_logs', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('login_as_user_id')->references('id')->on('users')->onDelete('cascade');
        });

     
        Schema::table('profiles', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('backups', function(Blueprint $table)
        {
            $table->dropForeign('backups_user_id_foreign');
        });

       

        Schema::table('activity_logs', function(Blueprint $table)
        {
            $table->dropForeign('activity_logs_user_id_foreign');
            $table->dropForeign('activity_logs_login_as_user_id_foreign');
        });


        Schema::table('profiles', function(Blueprint $table)
        {
            $table->dropForeign('profiles_user_id_foreign');
        });

    }
}

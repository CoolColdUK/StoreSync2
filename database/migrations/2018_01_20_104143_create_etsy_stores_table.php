<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEtsyStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etsy_stores', function (Blueprint $table) {
            Schema::dropIfExists('etsy_stores');
            $table->biginteger('id')->unsigned()->unique();
            $table->timestamps();

            $table->string('name',150);
            $table->string('primary_email',150);
            $table->integer('feedback_info_score');
            $table->integer('feedback_info_count');
            $table->boolean('use_new_inventory_endpoints');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etsy_users');
    }
}

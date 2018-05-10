<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEtsySectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etsy_sections', function (Blueprint $table) {
            Schema::dropIfExists('etsy_sections');
            $table->biginteger('id')->unsigned()->unique();
            $table->timestamps();

            $table->string('title', 255);
            $table->integer('rank');
            $table->integer('active_listing_count');

            $table->biginteger('etsy_store_id')->unsigned();
            $table->foreign('etsy_store_id')->references('id')
                ->on('etsy_stores')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etsy_sections');
    }
}

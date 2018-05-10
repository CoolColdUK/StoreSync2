<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEtsyListingImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etsy_listing_images', function (Blueprint $table) {
            Schema::dropIfExists('etsy_listing_images');

            $table->biginteger('id')->unsigned()->unique();
            $table->timestamps();

            $table->decimal('creation_tsz', 15, 2);
            $table->integer('rank');
            $table->integer('full_height');
            $table->integer('full_width');

            $table->string('url_fullxfull');
            $table->string('url_75x75');
            $table->string('url_170x135');
            $table->string('url_570xN');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etsy_listing_images');
    }
}

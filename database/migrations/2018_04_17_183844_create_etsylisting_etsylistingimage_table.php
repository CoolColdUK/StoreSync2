<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEtsylistingEtsylistingimageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etsylisting_etsylistingimage', function (Blueprint $table) {
            Schema::dropIfExists('etsylisting_etsylistingimage');
            $table->timestamps();


            $table->biginteger('listing_id')->unsigned();
            $table->foreign('listing_id')->references('id')
            ->on('etsy_listings')->onDelete('cascade');

            $table->biginteger('listing_image_id')->unsigned();
            $table->foreign('listing_image_id')->references('id')
                    ->on('etsy_listing_images')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etsylisting_etsylistingimage');
    }
}

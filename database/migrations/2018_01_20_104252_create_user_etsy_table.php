<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserEtsyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_etsy', function (Blueprint $table) {
            Schema::dropIfExists('user_etsy');
            $table->increments('id');
            $table->timestamps();
            
            $table->biginteger('etsy_store_id')->unsigned();
            $table->foreign('etsy_store_id')->references('id')
            ->on('etsy_stores')->onDelete('cascade');

            $table->biginteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')
                    ->on('users')->onDelete('cascade');
                    
            $table->string('api_key',50);
            $table->string('api_secret',50);
            $table->string('oauth_key',50);
            $table->string('oauth_secret',50);

            $table->boolean('perm_email_r')->default(0);
            $table->boolean('perm_listings_r')->default(0);
            $table->boolean('perm_listings_w')->default(0);
            $table->boolean('perm_listings_d')->default(0);
            $table->boolean('perm_transactions_r')->default(0);
            $table->boolean('perm_transactions_w')->default(0);
            $table->boolean('perm_billing_r')->default(0);
            $table->boolean('perm_profile_r')->default(0);
            $table->boolean('perm_profile_w')->default(0);

            $table->boolean('perm_address_r')->default(0);
            $table->boolean('perm_address_w')->default(0);
            $table->boolean('perm_favorites_rw')->default(0);
            $table->boolean('perm_shops_rw')->default(0);
            
            $table->boolean('perm_cart_rw')->default(0);
            $table->boolean('perm_recommend_rw')->default(0);
            $table->boolean('perm_feedback_r')->default(0);
            
            $table->boolean('perm_treasury_r')->default(0);
            $table->boolean('perm_treasury_w')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_etsy');
    }
}

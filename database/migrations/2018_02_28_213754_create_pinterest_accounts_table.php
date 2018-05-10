<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinterestAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinterest_accounts', function (Blueprint $table) {
            Schema::dropIfExists('pinterest_accounts');
            $table->biginteger('id')->unsigned()->unique();
            $table->timestamps();

            $table->string('username',100);
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->string('url',500);

            $table->string('image_url',500);
            $table->integer('image_width');
            $table->integer('image_height');

            $table->integer('counts_pins');
            $table->integer('counts_following');
            $table->integer('counts_followers');
            $table->integer('counts_boards');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pinterest_accounts');
    }
}

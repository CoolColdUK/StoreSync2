<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPinterestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_pinterest', function (Blueprint $table) {
            Schema::dropIfExists('user_pinterest');
            $table->increments('id');
            $table->timestamps();

            $table->biginteger('pinterest_account_id')->unsigned();
            $table->foreign('pinterest_account_id')->references('id')
                ->on('pinterest_accounts')->onDelete('cascade');

            $table->biginteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade');

            $table->string('api_key',75);
            $table->string('api_secret',75);
            $table->string('access_token',75);
            $table->string('refresh_token',75);

            $table->boolean('read_public')->default(0);
            $table->boolean('write_public')->default(0);
            $table->boolean('read_relationships')->default(0);
            $table->boolean('write_relationships')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_pinterest');
    }
}

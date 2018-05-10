<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinterestBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinterest_boards', function (Blueprint $table) {
            Schema::dropIfExists('pinterest_boards');
            $table->biginteger('id')->unsigned()->unique();
            $table->timestamps();


            $table->biginteger('pinterest_account_id')->unsigned();
            $table->foreign('pinterest_account_id')->references('id')
                ->on('pinterest_accounts')->onDelete('cascade');

            $table->string('name',100);
            $table->string('url',500);
            $table->text('description');
            $table->string('created',50);

            $table->string('image_url',500);
            $table->integer('image_width');
            $table->integer('image_height');

            $table->integer('counts_pins');
            $table->integer('counts_collaborators');
            $table->integer('counts_followers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pinterest_boards');
    }
}

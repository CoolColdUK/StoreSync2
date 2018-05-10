<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinterestPinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinterest_pins', function (Blueprint $table) {
            Schema::dropIfExists('pinterest_pins');
            $table->biginteger('id')->unsigned()->unique();
            $table->timestamps();


            $table->biginteger('pinterest_account_id')->unsigned();
            $table->foreign('pinterest_account_id')->references('id')
                ->on('pinterest_accounts')->onDelete('cascade');
                
            $table->biginteger('pinterest_board_id')->unsigned();
            $table->foreign('pinterest_board_id')->references('id')
                ->on('pinterest_boards')->onDelete('cascade');

                $table->string('link',500);
            $table->string('url',500);
            $table->text('note');
            $table->string('created',50);

            $table->string('color',50);

            $table->string('image_url',500);
            $table->integer('image_width');
            $table->integer('image_height');

            $table->integer('counts_saves');
            $table->integer('counts_comments');
            $table->string('media_type',20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pinterest_pins');
    }
}

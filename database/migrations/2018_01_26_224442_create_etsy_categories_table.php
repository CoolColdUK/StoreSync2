<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEtsyCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etsy_categories', function (Blueprint $table) {
            Schema::dropIfExists('etsy_categories');
            $table->biginteger('id')->unsigned()->unique();
            $table->timestamps();            
            
            $table->string('name', 255);
            $table->string('meta_title', 255)->default("");
            $table->text('meta_keywords');
            $table->text('meta_description');
            $table->text('page_description');
            $table->string('page_title')->default("");
            $table->string('category_name');
            $table->string('short_name');
            $table->text('long_name');
            $table->integer('num_children');
            $table->biginteger('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etsy_categories');
    }
}

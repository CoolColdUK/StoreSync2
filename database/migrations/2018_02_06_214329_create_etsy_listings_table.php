<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtsyListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etsy_listings', function (Blueprint $table) {
            Schema::dropIfExists('etsy_listings');
            $table->biginteger('id')->unsigned()->unique();
            $table->timestamps();

            //foreign keys
            $table->biginteger('etsy_category_id')->unsigned();
            $table->foreign('etsy_category_id')->references('id')
                ->on('etsy_categories')->onDelete('cascade');

            $table->biginteger('etsy_store_id')->unsigned();
            $table->foreign('etsy_store_id')->references('id')
                ->on('etsy_stores')->onDelete('cascade');

            $table->biginteger('etsy_section_id')->unsigned();

            $table->string('state', 25);
            $table->string('title', 255)->default('');
            $table->text('description');

            $table->decimal('creation_tsz', 15, 2);
            $table->decimal('ending_tsz', 15, 2);
            $table->decimal('original_creation_tsz', 15, 2);
            $table->decimal('last_modified_tsz', 15, 2)->default(0);

            $table->string('price', 25)->default(0.1);
            $table->string('currency_code', 25)->default('');
            $table->integer('quantity')->default(0);
            $table->string('tags', 300)->default('');

            $table->string('category_path', 300)->default('');
            $table->string('category_path_ids', 100)->default(0);
            $table->integer('taxonomy_id')->default(0);
            $table->integer('suggested_taxonomy_id')->default(0);
            $table->string('taxonomy_path', 255)->default('');

            $table->string('materials', 255)->default('');
            $table->integer('featured_rank')->default(0);
            $table->decimal('state_tsz', 15, 2)->default(0);
            $table->string('url', 255)->default('');
            $table->integer('views')->default(0);
            $table->integer('num_favorers')->default(0);

            $table->biginteger('shipping_template_id')->default(0);
            $table->integer('processing_min')->default(0);
            $table->integer('processing_max')->default(0);
            $table->string('who_made', 20)->default('');
            $table->boolean('is_supply')->default(false);
            $table->string('when_made', 20)->default('');


            $table->boolean('is_private')->default(false);
            $table->string('recipient', 50)->default('');
            $table->string('occasion', 50)->default('');
            $table->string('style', 255)->default('');
            $table->boolean('non_taxable')->default(false);

            $table->boolean('is_customizable')->default(false);
            $table->boolean('is_digital')->default(false);
            $table->string('file_data', 255)->default('');
            $table->boolean('can_write_inventory')->default(false);
            $table->boolean('has_variations')->default(false);
            $table->boolean('should_auto_renew')->default(false);
            $table->string('language', 50)->default('');

            $table->string('ss_sku', 100)->default('');
            $table->string('etsy_sku', 250)->default('');
            $table->string('internal_tags', 1000)->default('');
            $table->string('template', 150)->default('');
            
            //store inventory for draft listing only, as a whole
            $table->mediumText('inventory');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etsy_listings');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('region_id')->nullable();
            $table->string('name');
            $table->string('about', 255)->nullable();
            $table->boolean('all_categories')->default(false);
            $table->boolean('all_regions')->default(false);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('img_url')->nullable();
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('ads_category')
            ->onDelete('set null');
            $table->foreign('region_id')->references('id')->on('regions')
            ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertisers');
    }
}

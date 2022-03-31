<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('ad_image')->nullable();
            $table->string('ad_image_thump')->nullable();
            $table->integer('ad_image_sort')->nullable();


            $table->string('special_image')->nullable();
            $table->string('ads_link')->nullable();
            $table->string('video')->nullable();
            $table->integer('main_category_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('region_id')->nullable();
            $table->integer('area_id')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->integer('user_id')->nullable();
            $table->dateTime('invitation_start_time')->nullable();
            $table->dateTime('invitation_end_time')->nullable();
            $table->float('longitude')->nullable();
            $table->float('latitude')->nullable();
            $table->integer('special')->nullable();
            $table->dateTime('from')->nullable();
            $table->dateTime('to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}

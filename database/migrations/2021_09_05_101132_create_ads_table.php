<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('flag')->nullable();
            $table->string('title')->nullable();
            $table->text('brief')->nullable();
            $table->integer('image')->nullable();
            $table->string('ads_category_id')->nullable();
            $table->string('link')->nullable();
            $table->string('phone')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('snap_chat_url')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('website_url')->nullable();
            $table->integer('views')->nullable();
            $table->integer('pin')->nullable();
            $table->integer('is_admin')->nullable();
            $table->integer('country_id')->nullable();
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
        Schema::dropIfExists('ads');
    }
}

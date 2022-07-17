<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerReachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_reaches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('banner_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->foreign('banner_id')->references('id')
            ->on('banners')->onDelete('cascade');
            $table->foreign('user_id')->references('id')
            ->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banner_reaches');
    }
}

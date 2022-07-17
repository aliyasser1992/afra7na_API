<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meida', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->nullable();
            $table->integer('ads_id')->nullable();
            $table->string('image')->nullable();
            $table->string('thump')->nullable();
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
        Schema::dropIfExists('meida');
    }
}

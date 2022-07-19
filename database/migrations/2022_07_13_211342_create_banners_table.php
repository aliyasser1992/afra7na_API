<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('advertiser_id');
            $table->unsignedInteger('event_id');
            $table->string('type');
            $table->text('link')->nullable();
            $table->text('external_link')->nullable();
            $table->longText('content')->nullable();
            $table->integer('position')->default(1);
            $table->double('price', 5)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('advertiser_id')->references('id')
            ->on('advertisers')->onDelete('cascade');
            $table->foreign('event_id')->references('id')
            ->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banners');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('original_thumbnail_name');
            $table->string('watermark_image_name')->nullable();
            $table->string('watermark_image_thumbnail_name')->nullable();
            $table->string('watermark_text_name')->nullable();
            $table->string('watermark_text_thumbnail_name')->nullable();
            $table->string('cropped_name')->nullable();
            $table->string('cropped_thumbnail_name')->nullable();
            $table->string('original_name');
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
        Schema::dropIfExists('images');
    }
}

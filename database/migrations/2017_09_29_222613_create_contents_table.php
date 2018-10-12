<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('page_locale_id');
            $table->string('key');
            $table->string('id_html')->nullable();
            $table->string('class_html')->nullable();
            $table->longText('text')->nullable();
            $table->longText('header_inject')->nullable();
            $table->longText('footer_inject')->nullable();
            $table->unsignedInteger('priority');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('id');

            $table->foreign('page_locale_id')->references('id')->on('page_locales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents');
    }
}

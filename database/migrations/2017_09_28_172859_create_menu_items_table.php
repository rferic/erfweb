<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('menu_id');
            $table->string('lang');
            $table->string('label');
            $table->enum('type', ['internal', 'external']);
            $table->string('icon')->nullable();
            $table->unsignedInteger('page_locale_id')->nullable();
            $table->string('url_external')->nullable();
            $table->unsignedInteger('priority');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('menu_id')->references('id')->on('menus');
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
        Schema::dropIfExists('menu_items');
    }
}

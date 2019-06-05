<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('page_id')->unique()->nullable();
            $table->string('version');
            $table->string('vue_component');
            $table->string('type');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('id');
            $table->foreign('page_id')->references('id')->on('pages');
        });

        Schema::create('app_locales', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('app_id');
            $table->string('lang');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('app_id')->references('id')->on('apps');
        });

        Schema::create('app_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('app_id');
            $table->string('src');
            $table->string('title');
            $table->unsignedInteger('priority');
            $table->json('langs');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('app_id')->references('id')->on('apps');
        });

        Schema::create('app_user', function (Blueprint $table) {
            $table->primary(['app_id', 'user_id']);
            $table->unsignedInteger('app_id');
            $table->unsignedInteger('user_id');
            $table->boolean('active')->default(false);
            $table->timestamps();

            $table->foreign('app_id')->references('id')->on('apps');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_user');
        Schema::dropIfExists('app_images');
        Schema::dropIfExists('app_locales');
        Schema::dropIfExists('apps');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('message_parent_id')->nullable();
            $table->unsignedInteger('author_id');
            $table->unsignedInteger('receiver_id')->nullable();
            $table->string('status');
            $table->string('tag');
            $table->string('subject')->nullable();
            $table->longText('text')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('id');

            $table->foreign('message_parent_id')->references('id')->on('messages');
            $table->foreign('author_id')->references('id')->on('users');
            $table->foreign('receiver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}

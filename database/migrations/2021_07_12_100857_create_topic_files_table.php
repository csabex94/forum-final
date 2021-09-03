<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_conversation_id')->index();
            $table->foreignId('topic_id')->index();
            $table->foreignId('team_id')->index();
            $table->string('filename');
            $table->foreignId('user_id')->index();
            $table->string('original_filename');
            $table->integer('size');
            $table->boolean('starred')->default(0);
            $table->string('extension');
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
        Schema::dropIfExists('topic_files');
    }
}

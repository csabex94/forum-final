<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TopicReadsModifiedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topic_reads', function (Blueprint $table) {
            $table->dateTime('wseen')->nullable();
            $table->foreignId('topic_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topic_reads', function (Blueprint $table) {
            $table->dropColumn('wseen');
            $table->dropColumn('topic_id');
        });
    }
}

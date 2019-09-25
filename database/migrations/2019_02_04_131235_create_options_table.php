<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('custom_bet_id')->unsigned();
            $table->foreign('custom_bet_id')->references('id')->on('custom_bets')->onDelete('cascade');
            $table->string('value')->nullable();
            $table->string('status')->nullable();
            $table->integer('total_amount')->nullable();
            $table->dateTime('decided_date')->nullable();
            $table->integer('count')->nullable();
            $table->boolean('checked')->nullable()->default(false);
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
        Schema::dropIfExists('options');
    }
}

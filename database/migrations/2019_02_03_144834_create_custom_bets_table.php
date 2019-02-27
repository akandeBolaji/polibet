<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomBetsTable extends Migration
{
    /**
     * Run the migrations.s
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_bets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('total_amount')->nullable();
            $table->integer('count')->nullable()->default(0);
            $table->string('name')->nullable();
            $table->string('summary')->nullable();
            $table->string('outcome_status')->nullable();
            $table->integer('outcome_accepted_count')->default(1);
            $table->string('decided_by')->nullable();
            $table->dateTime('decided_date')->nullable();
            $table->integer('decidedby_id')->nullable();
            $table->integer('outcome')->nullable();
            $table->integer('minimum_stake')->nullable();
            $table->integer('maximum_part')->nullable();
            $table->dateTime('close_date')->nullable()->default(now());
            $table->dateTime('outcome_date')->nullable()->default(now());
            $table->string('random')->nullable();
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('custom_bets');
    }
}

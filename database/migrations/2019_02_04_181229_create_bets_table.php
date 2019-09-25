<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('option_id')->unsigned();
            $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
            $table->integer('custom_bet_id')->unsigned();
            $table->foreign('custom_bet_id')->references('id')->on('custom_bets')->onDelete('cascade');
            $table->integer('amount')->nullable();
            $table->integer('win_amount')->nullable();
            $table->string('status')->nullable()->default('pending');
            $table->boolean('disbursed')->nullable();
            $table->string('random')->nullable();
            $table->integer('referral_bonus_used')->nullable();
            $table->integer('signup_bonus_used')->nullable();
            $table->integer('account_balance_used')->nullable();
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
        Schema::dropIfExists('bets');
    }
}

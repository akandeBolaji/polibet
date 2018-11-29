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
            $table->integer('amount')->nullable();
            $table->integer('win_amount')->nullable();
            $table->string('status')->nullable();
            $table->string('betid')->nullable();
            $table->string('candidate')->nullable();
            $table->string('placed_by')->nullable();
            $table->string('placed_for')->nullable();
            $table->string('category')->nullable();
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

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            //$table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('email')->unique();
            $table->ipAddress('ip')->nullable();
            $table->string('phone')->unique();
            $table->string('full_name')->nullable();
            $table->string('password')->nullable();
            $table->string('provider')->nullable();
            $table->string('refer_id')->nullable();
            $table->string('bet_id')->nullable();
            $table->string('referrer_id')->nullable();
            $table->string('provider_unique_id')->nullable();
            $table->string('activation_token',64)->nullable();
            $table->string('status',25)->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}


<?php

use Faker\Generator as Faker;
use App\customBet;
use App\Bet;
use App\User;
use App\Option;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
        'ip' => $faker->ipv4,
        'phone' => $faker->phoneNumber,
        'status' => 'pending',
        'full_name' => $faker->name,
        'user_name' => $faker->userName,
        'rep' => rand(40, 100)
    ];
});

$factory->define(Bet::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return User::where('id', '<=' , 50)->inRandomOrder()->first()->id;
        },
        'amount' => rand(100, 5000),
        'status' => 'pending',
        'custom_bet_id' => function () {
            return customBet::where('id', '<=' , 50)->inRandomOrder()->first()->id;
        },
        //'candidate' => rand(1, 4),
        //'category' => rand(1,2),
    ];
});

$factory->define(customBet::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return User::where('id', '<=' , 50)->inRandomOrder()->first()->id;
        },
        'status' => 'approved',
        'custom_bet_id' => function () {
            return custOmBet::where('id', '<=' , 50)->inRandomOrder()->first()->id;
        },
        //'candidate' => rand(1, 4),
        //'category' => rand(1,2),
    ];
});

$factory->define(Option::class, function (Faker $faker) {
    return [
        'status' => 'approved',
        'custom_bet_id' => function () {
            return custOmBet::where('id', '<=' , 50)->inRandomOrder()->first()->id;
        },
        'value' => $faker->name,
        //'candidate' => rand(1, 4),
        //'category' => rand(1,2),
    ];
});








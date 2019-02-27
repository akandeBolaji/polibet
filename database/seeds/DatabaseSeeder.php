<?php

use Illuminate\Database\Seeder;
use App\customBet;
use App\Bet;
use App\User;
use App\Option;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
         // Un Guard model

         // Ask for db migration refresh, default is no
         if ($this->command->confirm('Do you wish to refresh migration before seeding, it will clear all old data ?')) {

             // Call the php artisan migrate:refresh using Artisan
             $this->command->call('migrate:refresh');

             $this->command->line("Data cleared, starting from blank database.");
         }
         $users = factory(User::class, 50)->create();

         $this->command->info('Users Created!');


         $this->command->info("Hurrah! Database has been seeded.");
    }
}

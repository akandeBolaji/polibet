<?php

use Illuminate\Database\Seeder;
use App\Account;
use App\Fund;
use App\Profile;
use App\Bet;
use App\Vote;
use App\signupBonus;
use App\referralBonus;
use App\User;

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
         $users = factory(User::class, 1235)->create();

         $this->command->info('Users Created!');

             factory(Bet::class, 98)->create([
                 'category' => 1,
                 'candidate' => 1
             ]);
             factory(Bet::class, 90)->create([
                'category' => 1,
                'candidate' => 2
            ]);
            factory(Bet::class, 40)->create([
                'category' => 2,
                'candidate' => 3
            ]);
            factory(Bet::class, 45)->create([
                'category' => 2,
                'candidate' => 4
            ]);



            factory(Vote::class, 98)->create([
                'category' => 1,
                'candidate' => 1
            ]);
            factory(Vote::class, 90)->create([
               'category' => 1,
               'candidate' => 2
           ]);
           factory(Vote::class, 40)->create([
               'category' => 2,
               'candidate' => 3
           ]);
           factory(Vote::class, 45)->create([
               'category' => 2,
               'candidate' => 4
           ]);


         $this->command->info("Hurrah! Database has been seeded.");
    }
}

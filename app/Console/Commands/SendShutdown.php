<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\temporaryShutdown;
use App\User;


class SendShutdown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:shutdown {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send shutdown e-mails to a user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->first();
        Mail::to($this->argument('email'))->send(new temporaryShutdown($user));
    }
}

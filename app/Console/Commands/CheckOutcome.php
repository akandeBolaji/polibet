<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\customBet;
use App\Option;
use App\Bet;
use App\Notifications\BetConcluded;

class CheckOutcome extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the status of events and perform some actions';

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
        $time = now()->addDays(-1);
        $bet_time = now();
        $options = Option::where('decided_date', '<=', $time)->where('status', 'pending')->get();
        $bets = customBet::where('close_date', '<=', $bet_time)->where('status', 'approved')->get();
        $accepted_options = Option::where('status', 'accepted')->where('checked', false)->get();
        //foreach( (array) $options as $option) {
        foreach( $options as $option) {
            $option->status = 'accepted';
            $option->Bet->outcome_status = 'accepted';
            $option->Bet->status = 'concluded';
            $option->save();
            $option->Bet->save();
            $users_id = \App\Bet::where('custom_bet_id', $option->Bet->id)->unique('user_id')->get('user_id');
            $users = \App\User::whereIn('id', $users_id)->get();
            foreach ($users as $user) {
                $five_minutes_ago = now()->subMinutes(5);
                if ($user->last_seen < $five_minutes_ago) {
                $user->notify(new BetConcluded($bet));
                }
            }
            //check if the bet is not disputed by the bet master and if the bet master didnt place any bet, send the bet master a notification if it all returns true
                $user = \App\User::where('id', $option->Bet->user_id)->whereNotIn('íd', $users)->get();
                if ($user) {
                $five_minutes_ago = now()->subMinutes(5);
                if ($user->last_seen < $five_minutes_ago) {
                $user->notify(new BetConcluded($bet));
                }
            }
            };
        foreach($bets as $bet) {
            $bet->status = 'closed';
            $bet->save();
            };
        foreach($accepted_options as $accepted_option) {
            $won_bets = $accepted_option->Bets;
            $bet = $accepted_option->Bet;
            foreach ($won_bets as $won_bet){
                $won_bet->status = 'won';
                $won_bet->win_amount = $won_bet->amount / $accepted_option->total_amount * $bet->total_amount;
                $won_bet->disbursed = true;
                $won_bet->User->Account->balance += $won_bet->win_amount;
                $won_bet->User->Account->win += $won_bet->win_amount;
                $won_bet->User->Account->save();
                $won_bet->save();
            }
             $lost_bets = Bet::where('custom_bet_id', $bet->id)->where('status', 'pending')->get();
             foreach ($lost_bets as $lost_bet){
                $lost_bet->status = 'lost';
                $lost_bet->save();
            }
            $accepted_option->checked = true;
            $accepted_option->save();
            };
    }
}

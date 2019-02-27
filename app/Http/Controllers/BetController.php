<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Notifications\OutcomeSubmitted;
use App\Notifications\DisputeSubmitted;
use App\Notifications\BetOutcomeReached;
use App\Notifications\BetConcluded;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubject;

class BetController extends Controller
{
    public function submitComment(Request $request){
        $dispute = \App\dispute::findorfail($request->dispute);
        $validation = Validator::make($request->all(),[
            'text' => 'required'
        ]
    );
    if($validation->fails())
    return response()->json(['message' => $validation->messages()->first()],422);

    $user = JWTAuth::parseToken()->authenticate();

   $comment = new \App\Comment;
   $comment->text = $request->text;
   $comment->user_id = $user->id;

   $dispute->comments->save($comment);


    return response()->json(['message' => 'Comment Successfully Submitted']);
}

    public function submitOutcome(Request $request){
        $bet = \App\customBet::with(['options.userdispute', 'options.useraccept', 'options.userbets'])->findorfail($request->custom_bet);
        $validation = Validator::make($request->all(),[
            'option' => 'required|numeric'
        ]
    );
    if($validation->fails())
    return response()->json(['message' => $validation->messages()->first()],422);

    $time = now()->subDays(1);
    $user = JWTAuth::parseToken()->authenticate();
    $option = \App\Option::findorfail($request->option);
    $userdispute = $option->userdispute()->count();
    $useraccept = $option->useraccept()->count();


    if ( ($user->id == $bet->user_id && $time < $bet->outcome_date) || $bet->outcome != null || $bet->status == 'concluded' || $bet->status == 'pending' || $bet->status == 'rejected' || $user->id == $bet->decidedby_id || $userdispute > 0 || $useraccept > 0)
    return response()->json(['message' => 'You are not allowed to submit an outcome'],422);

    if ($user->id != $bet->user_id) {
        $bet->User->rep -= 10;
        $bet->User->save();
    }

    $bet->outcome = $option->id;
    $bet->decided_by = $user->user_name;
    $bet->decided_date = now();
    $bet->decidedby_id = $user->id;
    $bet->outcome_status = 'pending';
    $option->status = 'pending';
    $option->decided_date = now();
    //create a cron job to change the outcome status, option status and bet status to accepted and concluded
    //reduce bet master reputation if outcome submitted by someone else

    $bet_count = \App\Bet::where('custom_bet_id', $bet->id)->unique('user_id')->count();
    if ($bet_count == 1){
        $bet->status = 'concluded';
        $bet->outcome_status = 'accepted';
        $option->status = 'accepted';
        //$user_bets = $option->Bets;

    }

    $bet->save();
    $option->save();

     $bet->user_rep = $bet->user()->value('rep');
     $bet->bet_master = $bet->user()->value('user_name');

     $users_id = \App\Bet::where('custom_bet_id', $bet->id)->where('user_id', '!=', $user->id)->unique('user_id')->get('user_id');
     $users = \App\User::whereIn('id', $users_id)->get();
     foreach ($users as $user) {
         $five_minutes_ago = now()->subMinutes(5);
         if ($user->last_seen < $five_minutes_ago) {
          $user->notify(new OutcomeSubmitted($bet));
         }
     }
     //check if the bet is not submitted by the bet master and if the bet master didnt place any bet, send the bet master a notification if it all returns true
     if ($bet->user_id != $user->id) {
         $user = \App\User::where('id', $bet->user_id)->whereNotIn('íd', $users)->get();
         if ($user) {
         $five_minutes_ago = now()->subMinutes(5);
         if ($user->last_seen < $five_minutes_ago) {
          $user->notify(new OutcomeSubmitted($bet));
         }
        }
     }

     //notify other users that an outcome has been submitted

    return response()->json(['message' => 'Outcome Successfully Submitted', 'bet' => $bet]);
}

    public function disputeOutcome(Request $request){
        $bet = \App\customBet::with(['options.userdispute', 'options.useraccept', 'options.userbets'])->findorfail($request->custom_bet);
        $validation = Validator::make($request->all(),[
            'reason' => 'required'
        ]
    );
    if($validation->fails())
    return response()->json(['message' => $validation->messages()->first()],422);

    $time = now()->addDays(-1);
    $user = JWTAuth::parseToken()->authenticate();
    $option = \App\Option::findorfail($bet->outcome);
    $userdispute = $option->userdispute()->count();
    $useraccept = $option->useraccept()->count();


    if ($time < $bet->decided_date || $bet->status == 'concluded' || $bet->status == 'pending' ||  $bet->status == 'rejected' || $user->id == $bet->decidedby_id || $userdispute > 0 || $useraccept > 0)
    return response()->json(['message' => 'You are no longer allowed to dispute this outcome'],422);

    $newDispute = new \App\Dispute;
    $newDispute->user_id = $user->id;
    $newDispute->reason = $request->reason;
    do {
        $random = str_random(6);
    } while ( \DB::table('disputes')->where('random',$random)->exists());
    $newDispute->random = $random;
    $option->disputes->save($newDispute);
    $bet->outcome_status = 'disputed';
    $bet->save();
    $option->status = 'disputed';
    $option->save();
     $bet->user_rep = $bet->user()->value('rep');
     $bet->bet_master = $bet->user()->value('user_name');

     $users_id = \App\Bet::where('custom_bet_id', $bet->id)->where('user_id', '!=', $user->id)->unique('user_id')->get('user_id');
     $users = \App\User::whereIn('id', $users_id)->get();
     foreach ($users as $user) {
         $five_minutes_ago = now()->subMinutes(5);
         if ($user->last_seen < $five_minutes_ago) {
          $user->notify(new DisputeSubmitted($bet));
         }

     }
     //check if the bet is not disputed by the bet master and if the bet master didnt place any bet, send the bet master a notification if it all returns true
     if ($bet->user_id != $user->id) {
         $user = \App\User::where('id', $bet->user_id)->whereNotIn('íd', $users)->get();
         if ($user) {
         $five_minutes_ago = now()->subMinutes(5);
         if ($user->last_seen < $five_minutes_ago) {
          $user->notify(new DisputeSubmitted($bet));
         }
        }
     }

     //notify users that a dispute has been made

    return response()->json(['message' => 'Dispute Successfully Created', 'bet' => $bet]);
}

public function acceptOutcome(Request $request){
    $bet = \App\customBet::with(['options.userdispute', 'options.useraccept', 'options.userbets'])->findorfail($request->custom_bet);
$time = now()->addDays(-1);
$user = JWTAuth::parseToken()->authenticate();
$option = \App\Option::findorfail($bet->outcome);
$userdispute = $option->userdispute()->count();
$useraccept = $option->useraccept()->count();


if ($time < $bet->decided_date || $bet->status == 'concluded' || $bet->status == 'pending' || $bet->status == 'rejected' || $user->id == $bet->decidedby_id || $userdispute > 0 || $useraccept > 0)
return response()->json(['message' => 'You are not allowed to accept this outcome'],422);

$newAccept = new \App\Accept;
$newAccept->user_id = $user->id;
$option->accepts->save($newAccept);
$bet->outcome_accepted_count += 1;
//$bet->save();
$bet_count = \App\Bet::where('custom_bet_id', $bet->id)->unique('user_id')->count() - 1;
$check_master = \App\Bet::where('custom_bet_id', $bet->id)->where('user_id', $bet->user_id)->count();
if ($check_master == 0) {
    $bet_count += 1;
}
if ($bet_count == $bet->outcome_accepted_count && $option->disputes()->count() == 0){
    $bet->status = 'concluded';
    $bet->outcome_status = 'accepted';
    $option->status = 'accepted';
    $option->save();
    $users_id = \App\Bet::where('custom_bet_id', $bet->id)->where('user_id', '!=', $user->id)->unique('user_id')->get('user_id');
    $users = \App\User::whereIn('id', $users_id)->get();
    foreach ($users as $user) {
        $five_minutes_ago = now()->subMinutes(5);
        if ($user->last_seen < $five_minutes_ago) {
         $user->notify(new BetConcluded($bet));
        }
    }
    //check if the bet is not disputed by the bet master and if the bet master didnt place any bet, send the bet master a notification if it all returns true
    if ($bet->user_id != $user->id) {
        $user = \App\User::where('id', $bet->user_id)->whereNotIn('íd', $users)->get();
        if ($user) {
        $five_minutes_ago = now()->subMinutes(5);
        if ($user->last_seen < $five_minutes_ago) {
         $user->notify(new BetConcluded($bet));
        }
       }
    }
}

$bet->save();
 $bet->user_rep = $bet->user()->value('rep');
 $bet->bet_master = $bet->user()->value('user_name');

return response()->json(['message' => 'Outcome Accepted Successfully', 'bet' => $bet]);
}

    public function addBet(Request $request){
        $bet = \App\customBet::with(['options.userdispute', 'options.useraccept', 'options.userbets'])->findorfail($request->custom_bet);
        $minimum = $bet->minimum_stake;
        $validation = Validator::make($request->all(),[
            'amount' => "required|numeric|min:$minimum",
            'option' => 'required|numeric'
        ]
    );
    if($validation->fails())
    return response()->json(['message' => $validation->messages()->first()],422);

    $time = now();

    if ($time > $bet->close_date || $bet->status == 'concluded' || $bet->status == 'closed' || $bet->status == 'rejected' || ( $bet->maximum_part != null && $bet->maximum_part == $bet->count))
    return response()->json(['message' => 'This bet has closed or invalid'],422);

    $user = JWTAuth::parseToken()->authenticate();
    $balance = $user->Account->balance;

    if( $balance < request('amount'))
    return response()->json(['message' => 'Insufficient funds, Please Load account and try again'],422);

    $option = \App\Option::findorfail($request->option);

    do {
        $random = str_random(6);
    } while ( \DB::table('bets')->where('random',$random)->exists());

    $newBet = new \App\Bet;
    $newBet->amount = request('amount');
    $newBet->option_id = request('option');
    $newBet->random =  $random;
    $newBet->custom_bet_id = $bet->id;
    //$newBet->save();
    $user->bets()->save($newBet);
    $option->count += 1 ;
    $option->total_amount += request('amount');
    $option->save();
    $bet->total_amount += request('amount');
    $bet->count += 1;
    $bet->save();
    $user->Account->balance -= request('amount');
    $user->Account->save();
     $bet->user_rep = $bet->user()->value('rep');
     $bet->bet_master = $bet->user()->value('user_name');



    return response()->json(['message' => 'Bet Placed Successfully', 'bet' => $bet]);
}
public function trendingBet() {
    $time = now();

    $bet = \App\customBet::where('status', 'approved')->where('close_date', '>=', $time)->orderBy('count', 'DESC')->get()->take(20);

    try {
      JWTAuth::parseToken()->authenticate();
     } catch (JWTException $e) {
      return response()->json(['message' => $bet, 'loggedOut' => true]);
      //return response(['authenticated' => false]);
    }

    return response()->json(['message' => $bet, 'loggedOut' => false]);
  }

    public function getBet($id) {
        $bet = \App\customBet::where('random', $id)->with('options')->firstorfail();
        $bet->user_rep = $bet->user()->value('rep');
        $bet->bet_master = $bet->user()->value('user_name');
        //$bet->user_name = $bet->user()->value('user_name');
        try {
          JWTAuth::parseToken()->authenticate();
         } catch (JWTException $e) {
          return response()->json(['message' => $bet, 'authenticated' => false]);
          //return response(['authenticated' => false]);
        }
        $bet = \App\customBet::where('random', $id)->with(['options.userdispute', 'options.useraccept', 'options.userbets'])->first();
        $user = JWTAuth::parseToken()->authenticate();
        $user->update([
            'last_seen' => new \DateTime(),
        ]);
        $account = $user->Account;
        $authenticated = true;
        //$disputes = $options->has('disputes')->where('user_id', $user->id)->first();
        $bet->user_rep = $bet->user()->value('rep');
        $bet->bet_master = $bet->user()->value('user_name');
        //$bet->user_name = $bet->user()->value('user_name');
        return response()->json(['message' => $bet, 'user' => $user, 'authenticated' => true]);
      }

      public function getDispute($id) {
        $dispute = \App\customBet::where('random', $id)->with(['comments.user', 'option.bet', 'user'])->first();
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->id != $dispute->user_id || $dispute == null)
        return response()->json(['message' => 'You are not allowed to view this dispute'],422);

        return response()->json(['message' => $dispute, 'user' => $user]);
      }

      public function createBet(Request $request){
          //$request = dd($request);

          //return response()->json(['message' => 'Check response for request object!', 'request' => $request]);


          $validation = Validator::make($request->all(),[
              'name' => 'required',
              'summary' => 'required',
              'minimum_stake' => 'required|numeric',
              'outcome' => 'required|min:2',
              'close_date' => 'required|date',
              'outcome_date' => 'required|date|after:close_date',
              'maximum_part' => 'nullable|numeric|min:2'
          ]
      );
      if($validation->fails())
      return response()->json(['message' => $validation->messages()->first()],422);

      $user = JWTAuth::parseToken()->authenticate();

      $customBet = new \App\customBet;
      do {
          $random = str_random(6);
      } while ( \DB::table('custom_bets')->where('random',$random)->exists());

      $customBet->name = request('name');
      $customBet->summary =  request('summary');
      $customBet->minimum_stake =  request('minimum_stake');
      $customBet->close_date =  date("Y-m-d H:i:s", (strtotime(request('close_date'))));
      $customBet->outcome_date =  date("Y-m-d H:i:s", (strtotime(request('outcome_date'))));
      $customBet->random =  $random;
      if (request('maximum_part')){
        $customBet->maximum_part =  request('maximum_part');
      }
      $user->customBets()->save($customBet);
      foreach(request('outcome') as $option) {
        $newOption = new \App\Option;
        $newOption->value = $option;
        $customBet->options()->save($newOption);
        };
        $five_minutes_ago = now()->subMinutes(5);
        if ($user->last_seen < $five_minutes_ago) {
           $user->notify(new BetOutcomeReached($bet))->delay($customBet->outcome_date);
        }
      return response()->json(['message' => 'Your Bet was created successfully', 'random' => $customBet->random]);
  }
}

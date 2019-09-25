<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubject;

class UserController extends Controller
{
    public function getBet($id) {
      $bet = \App\customBet::where('random', $id)->first();
      try {
        JWTAuth::parseToken()->authenticate();
       } catch (JWTException $e) {
        return response()->json(['message' => $bet, 'authenticated' => false]);
        //return response(['authenticated' => false]);
      }
      $user = JWTAuth::parseToken()->authenticate();
      $authenticated = true;
      if ($user->id == $bet->user_id){
         $bet->owner = true;
      }
      return response()->json(['message' => $bet, 'authenticated' => true]);
    }

    public function createBet(Request $request){
        //$request = dd($request);

        //return response()->json(['message' => 'Check response for request object!', 'request' => $request]);


        $validation = Validator::make($request->all(),[
            'bet_master' => 'required',
            'summary' => 'required',
            'minimum_stake' => 'required|numeric',
            'outcome1' => 'required',
            'outcome2' => 'required',
            'close_date' => 'required|date',
            'outcome_date' => 'required|date|after:close_date',
            'maximum_part' => 'numeric',
        ]
    );
    if($validation->fails())
    return response()->json(['message' => $validation->messages()->first()],422);

    $user = JWTAuth::parseToken()->authenticate();

    $customBet = new \App\customBet;
    do {
        $random = str_random(6);
    } while ( \DB::table('custom_bets')->where('random',$random)->exists());

    $customBet->bet_master =  request('bet_master');
    $customBet->summary =  request('summary');
    $customBet->minimum_stake =  request('minimum_stake');
    $customBet->outcome1 =  request('outcome1');
    $customBet->outcome2 =  request('outcome2');
    $customBet->close_date =  request('close_date');
    $customBet->outcome_date =  request('outcome_date');
    $customBet->random =  $random;
    if (request('maximum_part')){
      $customBet->maximum_part =  request('maximum_part');
    }
    $user->customBet()->save($customBet);
    return response()->json(['message' => 'Your Bet was created successfully', 'random' => $customBet->random]);
}
    public function withdrawFund(Request $request){
        $validation = Validator::make($request->all(),[
            'amount' => 'required|numeric',
        ],[
          'required' => 'We seem to have a problem processing your Topup. Please contact support.'
        ]
    );

       if($validation->fails())
      return response()->json(['message' => $validation->messages()->first()],422);


      $user = JWTAuth::parseToken()->authenticate();
      $account = $user->Account;
      $withdraw_total = \App\Withdrawal::where('user_id', $user->id)->sum('amount');
      $win_total = \App\Bet::where('user_id', $user->id)->where('status', 'won')->sum('win_amount');
      $withdrawable = $account->balance + $win_total - $withdraw_total;
      $win_balance = $win_total - $withdraw_total;
      if ($withdrawable < request('amount'))
      return response()->json(['message' => 'Insufficient cleared funds to withdraw'], 422);

      $newWithdrawal = new \App\Withdrawal;
      $newWithdrawal->amount = request('amount');
      $newWithdrawal->status = 'Processing';
      $user->withdrawals()->save($newWithdrawal);

      return response()->json(['message' => 'Your withdrawal request was sent successfully. You should receive within 2 working days.']);
    }

    public function addFund(Request $request){
        $validation = Validator::make($request->all(),[
            'amount' => 'required|numeric',
            'reference' => 'required',
            'transaction_id' => 'required',
        ],[
          'required' => 'We seem to have a problem processing your Topup. Please contact support.'
        ]
    );

       if($validation->fails())
      return response()->json(['message' => $validation->messages()->first()],422);

    $user = JWTAuth::parseToken()->authenticate();
    $account = $user->Account;
    $account->balance = request('amount') + $account->balance;
    $account->save();
    $newFund = new \App\Fund;
    $newFund->amount = request('amount');
    $newFund->reference = request('reference');
    $newFund->transaction_id = request('transaction_id');
    $user->funds()->save($newFund);

    return response()->json(['message' => 'You have successfully funded your account!']);
}

    public function addBetFriend(Request $request){
        $validation = Validator::make($request->all(),[
            'candidate' => 'required',
            'category' => 'required',
            'friend_id' => 'required',
            'amount' => 'required|numeric|min:100',
        ],[
          'amount.min' => 'You surprisingly got through our front end validation. Minimum stake amount still remains 100 naira'
        ]
    );

    if($validation->fails())
    return response()->json(['message' => $validation->messages()->first()],422);

    $user = JWTAuth::parseToken()->authenticate();
    $account = $user->Account;
    $account_balance = $account->balance;
    $time = now();
    if (\App\signupBonus::where('user_id', $user->id)->where('expires_at', '>' , $time)->value('amount') ){
    $signup_bonus = \App\signupBonus::where('user_id', $user->id)->where('expires_at', '>' , $time)->value('amount');
    }
    else {
    $signup_bonus = 0;
    };
    if (\App\referralBonus::where('user_id', $user->id )->where('expires_at', '>', now() )->sum('amount')) {
    $referral_bonus = \App\referralBonus::where('user_id', $user->id )->where('expires_at', '>', now() )->sum('amount');
    }
    else {
        $referral_bonus = 0;
    }
    $balance = $account_balance + $signup_bonus + $referral_bonus;
    if( $balance < request('amount'))
    return response()->json(['message' => 'Insufficient funds, Please Load account and try again', 'balance' => $balance],422);

    $friend = \App\User::where('bet_id', request('friend_id'))->first();

    if (!$friend)
    return response()->json(['message' => 'User does not exist'],422);

    $category = request('category');
    $category_exists = \App\Bet::where('user_id', $friend->id )->where('category', $category )->first();
    //return response()->json(['message' => 'signup bonus', 'category' => request('candidate')],422);
    if ($category_exists) {
        $candidate_check = \App\Bet::where('user_id', $friend->id )->where('category', $category )->first()->value('candidate');
        if ($candidate_check != request('candidate')){
            return response()->json(['message' => 'Try Again. Your friend"s previous bet for this category does not match!!'],422);
        }
    }

    if ($signup_bonus > 0) {
        if ($signup_bonus < request('amount')) {
            $balanceleft = request('amount') - $signup_bonus;
            $signup_bonus_used = $signup_bonus;
            if ($referral_bonus > 0) {
                if ($referral_bonus < $balanceleft ) {
                    $balanceleft2 = $balanceleft - $referral_bonus;
                    $referral_bonus_used = $referral_bonus;
                    if ($account_balance > 0) {
                       //$account_balance = $account_balance - $balanceleft2;
                       $account_balance_used = $balanceleft2;
                       $newBet = new \App\Bet;
                       $newBet->placed_by = $user->full_name;
                       $newBet->placed_for = $friend->full_name;
                       $newBet->betid = str_random(8);
                       $newBet->amount = request('amount');
                       $newBet->status = 'pending';
                       $newBet->signup_bonus_used =  $signup_bonus;
                       $newBet->referral_bonus_used =  $referral_bonus;
                       $newBet->account_balance_used =  $account_balance_used;
                       $account->balance = $account->balance - $account_balance_used;
                       $account->save();
                       \App\referralBonus::where('user_id', $user->id )->delete();
                       \App\signupBonus::where('user_id', $user->id )->delete();
                       $newBet->category = request('category');
                       $newBet->candidate = request('candidate');
                       $friend->bets()->save($newBet);

                       //done
                    }
                    else {
                        return response()->json(['message' => 'There is a problem with your request'],422);
                    }
                } elseif ($referral_bonus >= $balanceleft) {
                    $newBet = new \App\Bet;
                    $newBet->placed_by = $user->full_name;
                    $newBet->amount = request('amount');
                    //delete all signup bonus and referral bonus
                    //create a new referral bonus which expires in 24 hours
                    $newBet->signup_bonus_used =  $signup_bonus;
                    //referral bonus left = $referral_bonus - $balanceleft
                    $newBet->referral_bonus_used =  $balanceleft;
                    $newBet->category = request('category');
                    $newBet->betid = str_random(8);
                    $newBet->status = 'pending';
                    $newBet->placed_for = $friend->full_name;
                    $newBet->candidate = request('candidate');
                    $friend->bets()->save($newBet);
                    \App\referralBonus::where('user_id', $user->id )->delete();
                    \App\signupBonus::where('user_id', $user->id )->delete();
                    $newReferral = new \App\referralBonus;
                    $newReferral->amount = $referral_bonus -$balanceleft;
                    $newReferral->bet_balance = true;
                    $newReferral->expires_at = now()->addDays(1);
                    $user->referralBonus()->save($newReferral);
                    //done
                }
                else {
                    return response()->json(['message' => 'There is a problem with your request'],422);
                }
            } elseif ($referral_bonus == 0 && $account_balance > 0) {
                //$account_balance = $account_balance - $balanceleft;
                $newBet = new \App\Bet;
                $newBet->placed_by = $user->full_name;
                $newBet->amount = request('amount');
                //delete signup bonus
                $newBet->signup_bonus_used =  $signup_bonus;
                $newBet->status = 'pending';
                $newBet->betid = str_random(8);
                $newBet->placed_for = $friend->full_name;
                $newBet->account_balance_used =  $balanceleft;
                $account->balance = $account->balance - $balanceleft;
                $account->save();
                \App\signupBonus::where('user_id', $user->id )->delete();
                $newBet->category = request('category');
                $newBet->candidate = request('candidate');
                $friend->bets()->save($newBet);
                //done
            }
            else {
                return response()->json(['message' => 'There is a problem with your request'],422);
            }
        }
        elseif ($signup_bonus >= request('amount')) {
            //$signup_bonus_left = $signup_bonus - request('amount');
            //delete sign up bonus and create new one with expiry in 24 hours
            $newBet = new \App\Bet;
            $newBet->placed_by = $user->id;
            $newBet->amount = request('amount');
            $newBet->signup_bonus_used =  request('amount');
            $newBet->placed_for = $friend->full_name;
            $newBet->status = 'pending';
            $newBet->betid = str_random(8);
            $newBet->category = request('category');
            $newBet->candidate = request('candidate');
            $friend->bets()->save($newBet);
            \App\signupBonus::where('user_id', $user->id )->delete();
            $newSignup = new \App\signupBonus;
            $newSignup->amount = $signup_bonus - request('amount');
            $newSignup->bet_balance = true;
            $newSignup->expires_at = now()->addDays(1);
            $user->signupBonus()->save($newSignup);
            //done
        }
        else {
            return response()->json(['message' => 'There is a problem with your request'],422);
        }
     } elseif ($signup_bonus == 0 && $referral_bonus > 0 ) {
        if ($referral_bonus < request('amount')) {
            $balanceleft = request('amount') - $referral_bonus;
            if ($account_balance > 0) {
                //$account_balance = $account_balance - $balanceleft;
                $newBet = new \App\Bet;
                $newBet->placed_by = $user->full_name;
                $newBet->amount = request('amount');
                //delete referral bonus
                $newBet->referral_bonus_used =  $referral_bonus;
                $newBet->placed_for = $friend->full_name;
                $newBet->account_balance_used =  $balanceleft;
                $newBet->status = 'pending';
                $newBet->betid = str_random(8);
                $account->balance = $account->balance - $balanceleft;
                $account->save();
                \App\referralBonus::where('user_id', $user->id )->delete();
                $newBet->category = request('category');
                $newBet->candidate = request('candidate');
                $friend->bets()->save($newBet);
                       //done
            }
            else {
                return response()->json(['message' => 'There is a problem with your request'],422);
            }
        }
        elseif ($referral_bonus >= request('amount')) {
            $newBet = new \App\Bet;
            $newBet->placed_by = $user->full_name;
            $newBet->amount = request('amount');
            $user->referralBonus()->delete();
            //delete referral bonus and create new one
            //referral bonus left = $referral_bonus - request('amount')
            $newBet->referral_bonus_used = request('amount');
            $newBet->status = 'pending';
            $newBet->placed_for = $friend->full_name;
            $newBet->betid = str_random(8);
            $newBet->category = request('category');
            $newBet->candidate = request('candidate');
            $friend->bets()->save($newBet);
            \App\referralBonus::where('user_id', $user->id )->delete();
            $newReferral = new \App\referralBonus;
            $newReferral->amount = $referral_bonus - request('amount');
            $newReferral->bet_balance = true;
            $newReferral->expires_at = now()->addDays(1);
            $user->referralBonus()->save($newReferral);
            //done
        }
        else {
            return response()->json(['message' => 'There is a problem with your request'],422);
        }
     } elseif ($signup_bonus == 0 && $referral_bonus == 0 && $account_balance > 0) {
        //$account_balance = $account_balance - request('amount');
        $newBet = new \App\Bet;
        $newBet->placed_by = $user->full_name;
        $newBet->amount = request('amount');
        $newBet->status = 'pending';
        $newBet->placed_for = $friend->full_name;
        $newBet->betid = str_random(8);
        $newBet->account_balance_used = request('amount');
        $account->balance = $account->balance - request('amount');
        $account->save();
        $newBet->category = request('category');
        $newBet->candidate = request('candidate');
        $friend->bets()->save($newBet);
     }
     else {
        return response()->json(['message' => 'There is a problem with your request'],422);
    }

    $newVote = new \App\Vote;
    $newVote->category = request('category');
    $newVote->candidate = request('candidate');
    $user_check = \App\Vote::where('user_id', $friend->id )->where('category', request('category'))->exists();
    if ($user_check == null) {
     $newVote->user_id = $friend->id;
     $newVote->location = $friend->profile->location;
    }
    $newVote->save();

    return response()->json(['message' => 'You Successfully placed bet for a friend!']);

    }

    public function addBet(Request $request){
        //$request = dd($request);

        //return response()->json(['message' => 'Check response for request object!', 'request' => $request]);


        $validation = Validator::make($request->all(),[
            'candidate' => 'required',
            'category' => 'required',
            'amount' => 'required|numeric|min:100',
        ],[
          'amount.min' => 'You surprisingly got through our front end validation. Minimum stake amount still remains 100 naira'
        ]

    );

        if($validation->fails())
            return response()->json(['message' => $validation->messages()->first()],422);

        $user = JWTAuth::parseToken()->authenticate();
        $account = $user->Account;
        $account_balance = $account->balance;
        //return response()->json(['message' => 'signup bonus', 'signup' => $account_balance ],422);
        $time = now();
        //$signup_bonus = $user->Signup_bonus->amount;
       // $expires_at = now()->addDays(5);  ---use it when you create a referral bonus and signup bonus
       if (\App\signupBonus::where('user_id', $user->id)->where('expires_at', '>' , $time)->value('amount') ){
        $signup_bonus = \App\signupBonus::where('user_id', $user->id)->where('expires_at', '>' , $time)->value('amount');
       }
       else {
           $signup_bonus = 0;
       };
        //return response()->json(['message' => 'signup bonus', 'signup' => $signup_bonus ],422);
        if (\App\referralBonus::where('user_id', $user->id )->where('expires_at', '>', now() )->sum('amount')) {
        $referral_bonus = \App\referralBonus::where('user_id', $user->id )->where('expires_at', '>', now() )->sum('amount');
        }
        else {
            $referral_bonus = 0;
        }
        //return response()->json(['message' => 'signup bonus', 'signup' => $referral_bonus ],422);
        $balance = $account_balance + $signup_bonus + $referral_bonus;
        if( $balance < request('amount'))
         return response()->json(['message' => 'Insufficient funds, Please Load account and try again', 'balance' => $balance],422);

        //done check if user balance is greater than stake amount

        $category = request('category');
        $category_exists = \App\Bet::where('user_id', $user->id )->where('category', $category )->first();
        //return response()->json(['message' => 'signup bonus', 'category' => request('candidate')],422);
        if ($category_exists) {
            $candidate_check = \App\Bet::where('user_id', $user->id )->where('category', $category )->first()->value('candidate');
            if ($candidate_check != request('candidate')){
                return response()->json(['message' => 'Try Again. Your previous bet for this category does not match!!'],422);
            }
        }
        //return response()->json(['message' => 'Pass stage 2'],422);


        //check if the user has placed a bet in that category before, if bet has been placed, check if the request candidate is same has the previous candidate in that category

        //

        if ($signup_bonus > 0) {
            if ($signup_bonus < request('amount')) {
                $balanceleft = request('amount') - $signup_bonus;
                $signup_bonus_used = $signup_bonus;
                if ($referral_bonus > 0) {
                    if ($referral_bonus < $balanceleft ) {
                        $balanceleft2 = $balanceleft - $referral_bonus;
                        $referral_bonus_used = $referral_bonus;
                        if ($account_balance > 0) {
                           //$account_balance = $account_balance - $balanceleft2;
                           $account_balance_used = $balanceleft2;
                           $newBet = new \App\Bet;
                           $newBet->placed_by = $user->full_name;
                           $newBet->placed_for = $user->full_name;
                           $newBet->amount = request('amount');
                           $newBet->betid = str_random(8);
                           $newBet->status = 'pending';
                           $newBet->signup_bonus_used =  $signup_bonus;
                           $newBet->referral_bonus_used =  $referral_bonus;
                           $newBet->account_balance_used =  $account_balance_used;
                           $account->balance = $account->balance - $account_balance_used;
                           $account->save();
                           \App\referralBonus::where('user_id', $user->id )->delete();
                           \App\signupBonus::where('user_id', $user->id )->delete();
                           $newBet->category = request('category');
                           $newBet->candidate = request('candidate');
                           $user->bets()->save($newBet);

                           //done
                        }
                        else {
                            return response()->json(['message' => 'There is a problem with your request'],422);
                        }
                    } elseif ($referral_bonus >= $balanceleft) {
                        $newBet = new \App\Bet;
                        $newBet->placed_by = $user->full_name;
                        $newBet->amount = request('amount');
                        //delete all signup bonus and referral bonus
                        //create a new referral bonus which expires in 24 hours
                        $newBet->signup_bonus_used =  $signup_bonus;
                        //referral bonus left = $referral_bonus - $balanceleft
                        $newBet->referral_bonus_used =  $balanceleft;
                        $newBet->betid = str_random(8);
                        $newBet->category = request('category');
                        $newBet->placed_for = $user->full_name;
                        $newBet->status = 'pending';
                        $newBet->candidate = request('candidate');
                        $user->bets()->save($newBet);
                        \App\referralBonus::where('user_id', $user->id )->delete();
                        \App\signupBonus::where('user_id', $user->id )->delete();
                        $newReferral = new \App\referralBonus;
                        $newReferral->amount = $referral_bonus -$balanceleft;
                        $newReferral->bet_balance = true;
                        $newReferral->expires_at = now()->addDays(1);
                        $user->referralBonus()->save($newReferral);
                        //done
                    }
                    else {
                        return response()->json(['message' => 'There is a problem with your request'],422);
                    }
                } elseif ($referral_bonus == 0 && $account_balance > 0) {
                    //$account_balance = $account_balance - $balanceleft;
                    $newBet = new \App\Bet;
                    $newBet->placed_by = $user->full_name;
                    $newBet->amount = request('amount');
                    //delete signup bonus
                    $newBet->signup_bonus_used =  $signup_bonus;
                    $newBet->betid = str_random(8);
                    $newBet->status = 'pending';
                    $newBet->placed_for = $user->full_name;
                    $newBet->account_balance_used =  $balanceleft;
                    $account->balance = $account->balance - $balanceleft;
                    $account->save();
                    \App\signupBonus::where('user_id', $user->id )->delete();
                    $newBet->category = request('category');
                    $newBet->candidate = request('candidate');
                    $user->bets()->save($newBet);
                    //done
                }
                else {
                    return response()->json(['message' => 'There is a problem with your request'],422);
                }
            }
            elseif ($signup_bonus >= request('amount')) {
                //$signup_bonus_left = $signup_bonus - request('amount');
                //delete sign up bonus and create new one with expiry in 24 hours
                $newBet = new \App\Bet;
                $newBet->placed_by = $user->full_name;
                $newBet->amount = request('amount');
                $newBet->placed_for = $user->full_name;
                $newBet->signup_bonus_used =  request('amount');
                $newBet->betid = str_random(8);
                $newBet->status = 'pending';
                $newBet->category = request('category');
                $newBet->candidate = request('candidate');
                $user->bets()->save($newBet);
                \App\signupBonus::where('user_id', $user->id )->delete();
                $newSignup = new \App\signupBonus;
                $newSignup->amount = $signup_bonus - request('amount');
                $newSignup->bet_balance = true;
                $newSignup->expires_at = now()->addDays(1);
                $user->signupBonus()->save($newSignup);
                //done
            }
            else {
                return response()->json(['message' => 'There is a problem with your request'],422);
            }
         } elseif ($signup_bonus == 0 && $referral_bonus > 0 ) {
            if ($referral_bonus < request('amount')) {
                $balanceleft = request('amount') - $referral_bonus;
                if ($account_balance > 0) {
                    //$account_balance = $account_balance - $balanceleft;
                    $newBet = new \App\Bet;
                    $newBet->placed_by = $user->full_name;
                    $newBet->placed_for = $user->full_name;
                    $newBet->betid = str_random(8);
                    $newBet->amount = request('amount');
                    //delete referral bonus
                    $newBet->referral_bonus_used =  $referral_bonus;
                    $newBet->status = 'pending';
                    $newBet->account_balance_used =  $balanceleft;
                    $account->balance = $account->balance - $balanceleft;
                    $account->save();
                    \App\referralBonus::where('user_id', $user->id )->delete();
                    $newBet->category = request('category');
                    $newBet->candidate = request('candidate');
                    $user->bets()->save($newBet);
                           //done
                }
                else {
                    return response()->json(['message' => 'There is a problem with your request'],422);
                }
            }
            elseif ($referral_bonus >= request('amount')) {
                $newBet = new \App\Bet;
                $newBet->placed_by = $user->full_name;
                $newBet->placed_for = $user->full_name;
                $newBet->amount = request('amount');
                $user->referralBonus()->delete();
                //delete referral bonus and create new one
                //referral bonus left = $referral_bonus - request('amount')
                $newBet->referral_bonus_used = request('amount');
                $newBet->betid = str_random(8);
                $newBet->category = request('category');
                $newBet->status = 'pending';
                $newBet->candidate = request('candidate');
                $user->bets()->save($newBet);
                \App\referralBonus::where('user_id', $user->id )->delete();
                $newReferral = new \App\referralBonus;
                $newReferral->amount = $referral_bonus - request('amount');
                $newReferral->bet_balance = true;
                $newReferral->expires_at = now()->addDays(1);
                $user->referralBonus()->save($newReferral);
                //done
            }
            else {
                return response()->json(['message' => 'There is a problem with your request'],422);
            }
         } elseif ($signup_bonus == 0 && $referral_bonus == 0 && $account_balance > 0) {
            //$account_balance = $account_balance - request('amount');
            $newBet = new \App\Bet;
            $newBet->placed_by = $user->full_name;
            $newBet->placed_for = $user->full_name;
            $newBet->betid = str_random(8);
            $newBet->amount = request('amount');
            $newBet->status = 'pending';
            $newBet->account_balance_used = request('amount');
            $account->balance = $account->balance - request('amount');
            $account->save();
            $newBet->category = request('category');
            $newBet->candidate = request('candidate');
            $user->bets()->save($newBet);
         }
         else {
            return response()->json(['message' => 'There is a problem with your request'],422);
        }

        $newVote = new \App\Vote;
        $newVote->category = request('category');
        $newVote->candidate = request('candidate');
        $user_check = \App\Vote::where('user_id', $user->id )->where('category', request('category'))->exists();
        if ($user_check == null) {
         $newVote->user_id = $user->id;
         $newVote->location = $user->profile->location;
        }
        $newVote->save();

        return response()->json(['message' => 'Your bet was placed successfully!']);
    }
}

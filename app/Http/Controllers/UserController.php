<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use JWTAuth;

class UserController extends Controller
{

    protected $avatar_path = 'images/users/';

	public function index(){
		$users = \App\User::with('profile');

		if(request()->has('first_name'))
            $query->whereHas('profile',function($q) use ($request){
                $q->where('first_name','like','%'.request('first_name').'%');
            });

		if(request()->has('last_name'))
            $query->whereHas('profile',function($q) use ($request){
                $q->where('last_name','like','%'.request('last_name').'%');
            });

		if(request()->has('email'))
			$users->where('email','like','%'.request('email').'%');

        if(request()->has('status'))
            $users->whereStatus(request('status'));

        if(request('sortBy') == 'first_name' || request('sortBy') == 'last_name')
            $users->with(['profile' => function ($q) {
              $q->orderBy(request('sortBy'), request('order'));
            }]);
        else
            $users->orderBy(request('sortBy'),request('order'));

		return $users->paginate(request('pageLength'));
	}

    public function updateProfile(Request $request){

        $validation = Validator::make($request->all(),[
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'gender' => 'required|in:male,female'
        ]);

        if($validation->fails())
            return response()->json(['message' => $validation->messages()->first()],422);

        $user = JWTAuth::parseToken()->authenticate();
        $profile = $user->Profile;

        $profile->first_name = request('first_name');
        $profile->last_name = request('last_name');
        $profile->date_of_birth = request('date_of_birth');
        $profile->gender = request('gender');
        $profile->twitter_profile = request('twitter_profile');
        $profile->facebook_profile = request('facebook_profile');
        $profile->google_plus_profile = request('google_plus_profile');
        $profile->save();

        return response()->json(['message' => 'Your profile has been updated!','user' => $user]);
    }
    public function addFund(Request $request){
        $validation = Validator::make($request->all(),[
            'amount' => 'required',
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
            'amount' => 'required|numeric|min:5000',
        ],[
          'amount.min' => 'You surprisingly got through our front end validation. Minimum stake amount still remains 5000 naira'
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
            return response()->json(['message' => 'Try Again. Your previous bet for this category does not match!!'],422);
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
                       $newBet->amount = request('amount');
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
            $newBet->placed_for = $friend->full_name;
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
        $newBet->placed_for = $friend->full_name;
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
            'amount' => 'required|numeric|min:5000',
        ],[
          'amount.min' => 'You surprisingly got through our front end validation. Minimum stake amount still remains 5000 naira'
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
                        $newBet->category = request('category');
                        $newBet->placed_for = $user->full_name;
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
                    $newBet->amount = request('amount');
                    //delete referral bonus
                    $newBet->referral_bonus_used =  $referral_bonus;
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
                $newBet->category = request('category');
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
            $newBet->amount = request('amount');
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

    public function updateAvatar(Request $request){
        $validation = Validator::make($request->all(), [
            'avatar' => 'required|image'
        ]);

        if ($validation->fails())
            return response()->json(['message' => $validation->messages()->first()],422);

        $user = JWTAuth::parseToken()->authenticate();
        $profile = $user->Profile;

        if($profile->avatar && \File::exists($this->avatar_path.$profile->avatar))
            \File::delete($this->avatar_path.$profile->avatar);

        $extension = $request->file('avatar')->getClientOriginalExtension();
        $filename = uniqid();
        $file = $request->file('avatar')->move($this->avatar_path, $filename.".".$extension);
        $img = \Image::make($this->avatar_path.$filename.".".$extension);
        $img->resize(200, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($this->avatar_path.$filename.".".$extension);
        $profile->avatar = $filename.".".$extension;
        $profile->save();

        return response()->json(['message' => 'Avatar updated!','profile' => $profile]);
    }

    public function removeAvatar(Request $request){

        $user = JWTAuth::parseToken()->authenticate();

        $profile = $user->Profile;
        if(!$profile->avatar)
            return response()->json(['message' => 'No avatar uploaded!'],422);

        if(\File::exists($this->avatar_path.$profile->avatar))
            \File::delete($this->avatar_path.$profile->avatar);

        $profile->avatar = null;
        $profile->save();

        return response()->json(['message' => 'Avatar removed!']);
    }

    public function destroy(Request $request, $id){
        if(env('IS_DEMO'))
            return response()->json(['message' => 'You are not allowed to perform this action in this mode.'],422);

        $user = \App\User::find($id);

        if(!$user)
            return response()->json(['message' => 'Couldnot find user!'],422);

        if($user->avatar && \File::exists($this->avatar_path.$user->avatar))
            \File::delete($this->avatar_path.$user->avatar);

        $user->delete();

        return response()->json(['success','message' => 'User deleted!']);
    }

    public function dashboard(){
      $users_count = \App\User::count();
      $tasks_count = \App\Task::count();
      $recent_incomplete_tasks = \App\Task::whereStatus(0)->orderBy('due_date','desc')->limit(5)->get();
      return response()->json(compact('users_count','tasks_count','recent_incomplete_tasks'));
    }
}

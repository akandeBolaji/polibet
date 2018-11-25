<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubject;
use Validator;
use App\Notifications\Activation;
use App\Notifications\Activated;
use App\Notifications\PasswordReset;
use App\Notifications\PasswordResetted;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid Credentials! Please try again.'], 422);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'This is something wrong. Please try again!'], 500);
        }

        $user = \App\User::whereEmail(request('email'))->first();

        if($user->status == 'pending_activation')
            return response()->json(['message' => 'Your account hasn\'t been activated. Please check your email & activate account.'], 422);

        if($user->status == 'banned')
            return response()->json(['message' => 'Your account is banned. Please contact system administrator.'], 422);

        if($user->status != 'activated')
            return response()->json(['message' => 'There is something wrong with your account. Please contact system administrator.'], 422);

        return response()->json(['message' => 'You are successfully logged in!','token' => $token]);
    }

    public function getAuthUser(){
        //return response()->json('success', 201);
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['authenticated' => false],422);
        }

        if (\App\Vote::count() != 0){
            $votecategory_one = \App\Vote::where('category', 1)->where('user_id', '!=', 'null')->count();
            $votecategory_two = \App\Vote::where('category', 2)->where('user_id', '!=', 'null')->count();
            $votecandidate_one = \App\Vote::where('candidate', 1)->where('user_id', '!=', 'null')->count();
            $votecandidate_two = \App\Vote::where('candidate', 2)->where('user_id', '!=', 'null')->count();
            $votecandidate_three = \App\Vote::where('candidate', 3)->where('user_id', '!=', 'null')->count();
            $votecandidate_four = \App\Vote::where('candidate', 4)->where('user_id', '!=', 'null')->count();
            }
            else if (\App\Vote::count() == 0) {
                $votecategory_one = 0;
                $votecategory_two = 0;
                $votecandidate_one = 0;
                $votecandidate_two = 0;
                $votecandidate_three = 0;
                $votecandidate_four = 0;
            }

        $category_one = \App\Bet::where('category', 1)->sum('amount');
        $category_two = \App\Bet::where('category', 2)->sum('amount');
         $candidate_one = \App\Bet::where('candidate', 1)->sum('amount');
        $candidate_two = \App\Bet::where('candidate', 2)->sum('amount');
        $candidate_three = \App\Bet::where('candidate', 3)->sum('amount');
        $candidate_four = \App\Bet::where('candidate', 4)->sum('amount');




        $user = JWTAuth::parseToken()->authenticate();
        $profile = $user->Profile;
        $bet = $user->Bets;
        $bet_friends = \App\Bet::where('placed_by', $user->full_name)->where('user_id', '!=', $user->id)->get();
        $amount = ['candidate_one' => $candidate_one, 'candidate_two' => $candidate_two, 'candidate_three' => $candidate_three, 'candidate_four' => $candidate_four, 'category_one' => $category_one, 'category_two' => $category_two];
        $vote = ['candidate_one' => $votecandidate_one, 'candidate_two' => $votecandidate_two, 'candidate_three' => $votecandidate_three, 'candidate_four' => $votecandidate_four, 'category_one' => $votecategory_one, 'category_two' => $votecategory_two];
        $account = $user->Account;
        //$win_total = \App\Bet::where('user_id', $user->id)->where('status', 'won')->sum('win_amount');
        //if ($win_total){
            //$withdraw = $win_total;
        //}
        //else {
           // $withdraw = 0;
        //}
        $referral_bonus = $user->ReferralBonus;
        $signup_bonus = $user->SignupBonus;
        $referral_total = \App\referralBonus::where('user_id', $user->id)->sum('amount');
        $withdraw_total = \App\Withdrawal::where('user_id', $user->id)->sum('amount');
        $win_total = \App\Bet::where('user_id', $user->id)->where('status', 'won')->sum('win_amount');
        $balance = $account->balance + $signup_bonus->amount + $referral_total + $win_total - $withdraw_total;
        $withdrawable = $account->balance + $win_total - $withdraw_total;
        $funds = $user->Funds;
        $withdrawals = $user->withdrawals;

        $referrals = \App\User::where('referrer_id', $user->refer_id)->get();

        if(!$referrals) {
            $referrals_name = [];
        }
        else {
        $referrals_name =  \App\User::where('referrer_id', $user->refer_id)->get(['full_name', 'id']);
        }

        return response()->json(compact('user','profile', 'withdrawals', 'bet', 'withdrawable', 'balance','funds', 'bet_friends', 'referrals_name', 'vote', 'amount', 'account', 'referral_bonus', 'signup_bonus'), 201);
    }

    public function getStats(){

        //return response()->json('success', 201);
        //$user = auth()->user();

        //$total_bet_candidate = \App\bet::where('candidate', request('candidate'))->sum('amount');
        //$total_bet_category = \App\bet::where('category', request('category'))->sum('amount');
        //$win_amount = ((request('amount')/$total_bet_candidate) * $total_bet_category);
        $users = \App\User::count();
        if (\App\Vote::count() != 0){
        $votecategory_one = \App\Vote::where('category', 1)->where('user_id', '!=', 'null')->count();
        $votecategory_two = \App\Vote::where('category', 2)->where('user_id', '!=', 'null')->count();
        $votecandidate_one = \App\Vote::where('candidate', 1)->where('user_id', '!=', 'null')->count();
        $votecandidate_two = \App\Vote::where('candidate', 2)->where('user_id', '!=', 'null')->count();
        $votecandidate_three = \App\Vote::where('candidate', 3)->where('user_id', '!=', 'null')->count();
        $votecandidate_four = \App\Vote::where('candidate', 4)->where('user_id', '!=', 'null')->count();
        }
        else if (\App\Vote::count() == 0) {
            $votecategory_one = 0;
            $votecategory_two = 0;
            $votecandidate_one = 0;
            $votecandidate_two = 0;
            $votecandidate_three = 0;
            $votecandidate_four = 0;
        }
        $category_one = \App\Bet::where('category', 1)->sum('amount');
        $category_two = \App\Bet::where('category', 2)->sum('amount');
        $candidate_one = \App\Bet::where('candidate', 1)->sum('amount');
        $candidate_two = \App\Bet::where('candidate', 2)->sum('amount');
        $candidate_three = \App\Bet::where('candidate', 3)->sum('amount');
        $candidate_four = \App\Bet::where('candidate', 4)->sum('amount');

        $amount = ['candidate_one' => $candidate_one, 'candidate_two' => $candidate_two, 'candidate_three' => $candidate_three, 'candidate_four' => $candidate_four, 'category_one' => $category_one, 'category_two' => $category_two];
        $vote = ['candidate_one' => $votecandidate_one, 'candidate_two' => $votecandidate_two, 'candidate_three' => $votecandidate_three, 'candidate_four' => $votecandidate_four, 'category_one' => $votecategory_one, 'category_two' => $votecategory_two];

        return response()->json(compact('users','amount', 'vote'), 201);
    }

    public function check()
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response(['authenticated' => false]);
        }

        return response(['authenticated' => true]);
    }

    public function logout()
    {

        try {
            $token = JWTAuth::getToken();

            if ($token) {
                JWTAuth::invalidate($token);
            }

        } catch (JWTException $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(['message' => 'You are successfully logged out!']);
    }

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'full_name' => 'required',
            'age' => 'required',
            'bank_name' => 'required',
            'account_name' => 'required|same:full_name',
            'account_number' => 'required',
            'location' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        if($validation->fails())
            return response()->json(['message' => $validation->messages()->first()],422);

        $user = \App\User::create([
            'phone' => request('phone'),
            'ip' => request()->ip(),
            'email' => request('email'),
            'full_name' => request('full_name'),
            'status' => 'pending_activation',
            'password' => bcrypt(request('password'))
        ]);

        $referrer = \DB::table('users')->where('refer_id','=',request('referred_id'))->first();


        if ($referrer) {
          $user->referrer_id = request('referrer_id');
        }
        //do {
            //0221965318
           // $bet_id = str_random(10);
           // $check_betid = \DB::table('users')->where('bet_id',$bet_id)->get();
       // } while ( !empty($check_betid));
        do {
            $bet_id = str_random(4);
        } while ( \DB::table('users')->where('bet_id',$bet_id)->exists());

        do {
            $refer_id = str_random(10);
        } while ( \DB::table('users')->where('refer_id',$refer_id)->exists());

        do {
            $activation_token = str_random(64);
        } while ( \DB::table('users')->where('activation_token',$activation_token)->exists());
        $user->bet_id = $bet_id;
        $user->refer_id = $refer_id;
        $user->activation_token = $activation_token;
        $user->save();
        $profile = new \App\Profile;
        $profile->email = request('email');
        $profile->gender = request('gender');
        $profile->accredited = request('accredited');
        $profile->full_name = request('full_name');
        $profile->age = request('age');
        $profile->phone = request('phone');
        $profile->bank_name = request('bank_name');
        $profile->account_name = request('account_name');
        $profile->account_number = request('account_number');
        $profile->location = request('location');
        $profile->referred_by = request('referred_by');
        $user->profile()->save($profile);
        $account = new \App\Account;
        $account->balance = 0;
        $user->account()->save($account);
        $users = \App\User::count();
        if ($users < 10001 ){
            $signup_bonus = new \App\signupBonus;
            $signup_bonus->amount = 2000;
            $signup_bonus->expires_at = now()->addDays(5);
            $user->signupBonus()->save($signup_bonus);
        }

        $user->notify(new Activation($user));

        return response()->json(['message' => 'You have been registered successfully. Please check your email for activation!']);
    }

    public function activate($activation_token){
        $user = \App\User::whereActivationToken($activation_token)->first();

        if(!$user)
            return response()->json(['message' => 'Invalid activation token!'],422);

        if($user->status == 'activated')
            return response()->json(['message' => 'Your account has already been activated!'],422);

        if($user->status != 'pending_activation')
            return response()->json(['message' => 'Invalid activation token!'],422);

        $user->status = 'activated';
        $user->save();
        $referrer_id =  $user->referrer_id;
        if ($referrer_id) {
           $referrer = \App\User::where('refer_id', $referrer_id)->first();
           $countIP = \App\User::where('ip', $user->ip)->count();
           if ($countIP == 1) {
          if ($referrer) {
              $referrer_bonus = new \App\referralBonus;
              $referrer_bonus->amount = 100;
              $referrer_bonus->expires_at = now()->addDays(5);
              $referrer_bonus->referred = $user->full_name;
              $referrer->referralBonus()->save($referrer_bonus);
          }
        }
        }
        //$user->notify(new Activated($user));

        return response()->json(['message' => 'Your account has been activated!']);
    }

    public function password(Request $request){

        $validation = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if($validation->fails())
            return response()->json(['message' => $validation->messages()->first()],422);

        $user = \App\User::whereEmail(request('email'))->first();

        if(!$user)
            return response()->json(['message' => 'We couldn\'t found any user with this email. Please try again!'],422);

        $token = str_random(64);;
        \DB::table('password_resets')->insert([
            'email' => request('email'),
            'token' => $token
        ]);
        $user->notify(new PasswordReset($user,$token));

        return response()->json(['message' => 'We have sent a reminder email. Please check your inbox!']);
    }

    public function validatePasswordReset(Request $request){
        $validate_password_request = \DB::table('password_resets')->where('token','=',request('token'))->first();

        if(!$validate_password_request)
            return response()->json(['message' => 'Invalid password reset token!'],422);

        if(date("Y-m-d H:i:s", strtotime($validate_password_request->created_at . "+30 minutes")) < date('Y-m-d H:i:s'))
            return response()->json(['message' => 'Password reset token is expired. Please request reset password again!'],422);

        return response()->json(['message' => '']);
    }


    public function getReferrer(Request $request){
       // return response()->json(['refrrer' => request('referred_id')],422);
        $referrer = \DB::table('users')->where('refer_id','=',request('referrer_id'))->first();

        if(!$referrer)
            return response()->json(['message' => 'No such user!'],422);

        $referrer_name = \DB::table('users')->where('refer_id','=',request('referrer_id'))->value('full_name');

        return response()->json([ 'message' => $referrer_name]);
    }

    public function reset(Request $request){

        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        if($validation->fails())
            return response()->json(['message' => $validation->messages()->first()],422);

        $user = \App\User::whereEmail(request('email'))->first();

        if(!$user)
            return response()->json(['message' => 'We couldn\'t found any user with this email. Please try again!'],422);

        $validate_password_request = \DB::table('password_resets')->where('email','=',request('email'))->where('token','=',request('token'))->first();

        if(!$validate_password_request)
            return response()->json(['message' => 'Invalid password reset token!'],422);

        if(date("Y-m-d H:i:s", strtotime($validate_password_request->created_at . "+30 minutes")) < date('Y-m-d H:i:s'))
            return response()->json(['message' => 'Password reset token is expired. Please request reset password again!'],422);

        $user->password = bcrypt(request('password'));
        $user->save();

        //$user->notify(new PasswordResetted($user));

        return response()->json(['message' => 'Your password has been reset. Please login again!']);
    }

    public function changePassword(Request $request){
        if(env('IS_DEMO'))
            return response()->json(['message' => 'You are not allowed to perform this action in this mode.'],422);

        $validation = Validator::make($request->all(),[
            'current_password' => 'required',
            'new_password' => 'required|confirmed|different:current_password|min:6',
            'new_password_confirmation' => 'required|same:new_password'
        ]);

        if($validation->fails())
            return response()->json(['message' => $validation->messages()->first()],422);

        $user = JWTAuth::parseToken()->authenticate();

        if(!\Hash::check(request('current_password'),$user->password))
            return response()->json(['message' => 'Old password does not match! Please try again!'],422);

        $user->password = bcrypt(request('new_password'));
        $user->save();

        return response()->json(['message' => 'Your password has been changed successfully!']);
    }
}

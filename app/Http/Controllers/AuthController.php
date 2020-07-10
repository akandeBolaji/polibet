<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubject;
use Validator;
use App\Notifications\Activation;
use App\Notifications\Registration;
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

        if($user->status == 'banned')
            return response()->json(['message' => 'Your account is banned. Please contact system administrator.'], 422);

        if($user->status != 'activated' && $user->status != 'pending_activation')
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
        $user = JWTAuth::parseToken()->authenticate();
        $profile = $user->Profile;
        $bet = $user->bets()->with(['option', 'custom_bet'])->get();
        $account = $user->Account;
        $funds = $user->Funds;
        $disputes = $user->Disputes;
        $customBet = $user->CustomBets;
        $withdrawals = $user->withdrawals;
        //$bets = \App\Bet::with(['option.bet'])->where('user_id', $user->id)->get();
        return response()->json(['user' => $user, 'bet' => $bet], 201);
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

    public function editUser(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_name' => 'nullable|unique:users',
            'account_number' => 'nullable|numeric',
        ]);

        if($validation->fails())
            return response()->json(['message' => $validation->messages()->first()],422);


        $user = JWTAuth::parseToken()->authenticate();

        if ($request->user_name) {
        $user->user_name = $request->user_name;
        $user->save();
        }

        if ($request->account_name && $request->account_number && $request->bank_name) {
        $user->Profile->account_name = $request->account_name;
        $user->Profile->account_number = $request->account_number;
        $user->Profile->bank_name = $request->bank_name;
        $user->Profile->save();
        }

        return response()->json(['message' => 'Edit successful']);
    }

    public function verifyAccount(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        do {
            $activation_token = str_random(64);
        } while ( \DB::table('users')->where('activation_token',$activation_token)->exists());
        $user->activation_token = $activation_token;
        $user->save();
        $user->notify(new Activation($user));

        return response()->json(['message' => 'A verification email has been sent to your registered email address. Do click on this message to complete the verification process']);
    }

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'full_name' => 'required',
            'user_name' => 'required|unique:users',
            'age' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        if($validation->fails())
            return response()->json(['message' => $validation->messages()->first()],422);

        $user = \App\User::create([
            'phone' => request('phone'),
            'ip' => request()->ip(),
            'email' => request('email'),
            'full_name' => request('full_name'),
            'user_name' => request('user_name'),
            'status' => 'pending_activation',
            'password' => bcrypt(request('password'))
        ]);
        $profile = new \App\Profile;
        $profile->email = request('email');
        $profile->gender = request('gender');
        $profile->full_name = request('full_name');
        $profile->age = request('age');
        $profile->phone = request('phone');
        $user->profile()->save($profile);
        $account = new \App\Account;
        $account->balance = 0;
        $user->account()->save($account);

        //$user->notify(new Registration($user));

        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);


        return response()->json(['message' => 'You have been registered successfully.', 'token' => $token]);
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

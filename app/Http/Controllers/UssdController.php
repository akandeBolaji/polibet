<?php

namespace App\Http\Controllers;
//ngrok http -host-header=rewrite polibet.site:80

//use Illuminate\Http\Request;
use Log;

class UssdController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sessionId   = $_POST["sessionId"];
        $serviceCode = $_POST["serviceCode"];
        $phoneNumber = $_POST["phoneNumber"];
        $text        = $_POST["text"];
        Log::debug(['text', $text]);
        $user_exists = \App\User::where('phone', $phoneNumber)->first();
        if (!$user_exists && $text == "") {
            $response  = "CON Welcome To Cheetah, the first USSD Crypto wallet \n";
            $response .= "1. Open Account \n";
        }

        else if (!$user_exists && $text == "1") {
            $response  = "CON Enter your name \n";
        }

        else if ( $text == "" ) {
            $response  = "CON Welcome Client, what would you want to check \n";
            $response .= "1. My Account \n";
            $response .= "2. My phone number";
        }
        else if ($text == "1") {
            $response  = "CON Choose account information you want to view \n";
            $response .= "1. Account Number \n";
            $response .= "2. Account Balance \n";
        }
        else if ($text == "2") {
            //note that we are using the $phoneNumber variable we got form the HTTP POST data.
            $response = "END Your phone number is $phoneNumber\n";
        }
        else if ($text == "1*1") {
            $response = "END Your account number is ACC1001\n";
        }
        else if ($text == "1*2") {
            $response = "END Your balance is USD 10.78\n";
        }
        header('Content-type: text/plain');
        echo $response;
        //return view('home');
    }
}

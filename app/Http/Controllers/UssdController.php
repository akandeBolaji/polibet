<?php
//ngrok http -host-header=rewrite polibet.site:80
namespace app\Http\Controllers;

use Illuminate\Http\Request;
use Log;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ussd_logs;
use App\ussd_menu;
use App\ussd_menu_items;
use App\ussd_response;
use App\ussd_user;

class UssdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        error_reporting(0);
        header('Content-type: text/plain');
        set_time_limit(100);

        //get inputs
        $sessionId = $_REQUEST["sessionId"];
        $serviceCode = $_REQUEST["serviceCode"];
        $phoneNumber = $_REQUEST["phoneNumber"];
        $text = $_REQUEST["text"];   //


        $data = ['phone' => $phoneNumber, 'text' => $text, 'service_code' => $serviceCode, 'session_id' => $sessionId];
//        print_r($data);
//        exit;
        //log USSD request
        ussd_logs::create($data);

        //verify that the user exists
        $no = substr($phoneNumber, -9);

        $user = ussd_user::where('phone', $phoneNumber)->first();
        Log::debug(['text', $text, 'user', $user]);

        if ((!$user || !$user->name || $user->pin == 0) && $text == '') {
                $response  = "CON Welcome To Cheetah, the first USSD Crypto wallet \n";
                $response .= "1. Open Wallet \n";

                $output .= $response;
                header('Content-type: text/plain');
                echo $output;
                exit;
        }


        else if ((!$user || !$user->name || $user->pin == 0) && $text == "1") {
            $response  = "CON Enter your name \n";
             $usr = array();
             //$usr['phone'] = "0".$no;
             $result = explode("*", $text);
             if (empty($result)) {
                $message = $text;
            } else {
                end($result);
                // move the internal pointer to the end of the array
                $message = current($result);
            }
            if (!$user) {
             $usr['phone'] = $phoneNumber;
             $usr['session'] = 5;
             //$usr['notify'] = 'Your Wallet was created successfully';
             //$usr['name'] = $message;
             $usr['progress'] = 1;
             $usr['confirm_from'] = 0;
             $usr['menu_item_id'] = 0;
             $user = ussd_user::create($usr);
            }
            else {
                $user->session = 5;
                $user->progress = 1;
                $user->save();
            }
             $output .= $response;
             header('Content-type: text/plain');
             echo $output;
             exit;
             //$new_user = ussd_user::create($usr);
        }


        //check if text is empty
        else if ($user && self::user_is_starting($text)) {
            //lets get the home menu
            //reset user
            self::resetUser($user);
            //user authentication
            $message = '';
            //get menu items getMenuAndItems(user, menu_id)
            $response = self::getMenuAndItems($user,1);

            //get the home menu
            self::sendResponse($response, 1, $user);
        } else {

            //message is the latest stuff
            $result = explode("*", $text);
            if (empty($result)) {
                $message = $text;
            } else {
                end($result);
                // move the internal pointer to the end of the array
                $message = current($result);
            }

            switch ($user->session) {

                case 0 :
                    //neutral user
                    break;
                case 1 :
                    $response = self::continueUssdMenuProcess($user, $message);
                    //echo "Main Menu";
                    break;
                case 2 :
                    //confirm USSD Process
                    $response = self::confirmUssdProcess($user, $message);
                    break;
                case 3 :
                    //Go back menu
                    if (!is_numeric($message) || strlen($message) != 4) {
                        $response  = "END Invalid Pin input. Input should have a length of four and contain numbers alone\n";
                        $output .= $response;
                        header('Content-type: text/plain');
                        echo $output;
                        exit;
                    }
                    else {
                    $user->pin = $message;
                    $user->notify = 'Your Wallet was created successfully';
                    $user->save();
                    $response = self::confirmGoBack($user, $message);
                    }
                    break;
                case 4 :
                    //Go back menu
                    $response = self::confirmGoBack($user, $message);
                    break;
                case 5 :
                //Go back menu
                    $response  = "CON Enter a four digit pin \n";
                    $user->name = $message;
                    $user->session = 3;
                    //$user->notify = 'Your Wallet was created suessfully';
                    $user->save();
                    $output .= $response;
                    header('Content-type: text/plain');
                    echo $output;
                    exit;

                break;
                case 6 :
                //confirm USSD Pin
                $response = self::confirmUssdPin($user, $message);
                break;
                default:
                    break;
            }

            self::sendResponse($response, 1, $user);
        }

    }

    //confirm go back
    public function confirmGoBack($user, $message)
    {

        if (self::validationVariations($message, 1, "yes")) {
            //go back to the main menu
            //self::resetUser($user);

            $user->menu_id = 1;
            $user->session = 1;
            $user->progress = 1;
            $user->save();
            //get home menu
            $menu = ussd_menu::find(1);
            $menu_items = self::getMenuItems($menu->id);
            $i = 1;
            $response = $menu->title . PHP_EOL;
            foreach ($menu_items as $key => $value) {
                $response = $response . $i . ": " . $value->description . PHP_EOL;
                $i++;
            }
            self::sendResponse($response, 1, $user);
            exit;

        } elseif (self::validationVariations($message, 2, "no")) {
            $response = "Thank you for using our service";
            self::sendResponse($response, 3, $user);

        } else {
            $response = $user->notify;
            self::sendResponse($response, 2, $user);
            exit;
        }

    }


    //confirmUssdProcess
    public function confirmUssdProcess($user, $message)
    {
        $menu = ussd_menu::find($user->menu_id);
        if (self::validationVariations($message, 1, "yes")) {
            //if confirmed

            if (self::postUssdConfirmationProcess($user)) {
                $response = $menu->confirmation_message;
            } else {
                $response = $user->notify;
            }

            self::resetUser($user);
            self::sendResponse($response, 2, $user);

        } elseif (self::validationVariations($message, 2, "no")) {
            $response = self::nextMenuSwitch($user, $menu);
            return $response;

        } else {
            //not confirmed
            $response = "We could not understand your response";
            //restart the process
            $output = self::confirmBatch($user, $menu);

            $response = $response . PHP_EOL . $output;
            return $response;
        }


    }

    public function confirmUssdPin($user, $message)
    {
        $menu = ussd_menu::find($user->menu_id);
        self::storeUssdResponse($user,$message);


            if (self::postUssdConfirmationProcess($user)) {
                $response = $menu->confirmation_message;
            } else {
                $response = $user->notify;
            }

            self::resetUser($user);
            self::sendResponse($response, 2, $user);



    }


    //post ussd confirmation, define your processes

    public function postUssdConfirmationProcess($user)
    {

        switch ($user->confirm_from) {
            case 1:
                $no = substr($user->phone, -9);

                $data['email'] = "0".$no."@agin.com";

                User::create($data);
                return true;
                break;
            case 13:
            $menu_items = self::getMenuItems(13);

            $old_pass = ussd_response::whereUserIdAndMenuIdAndMenuItemId($user->id, 13, 8)->orderBy('id', 'DESC')->first();
            $new_pass = ussd_response::whereUserIdAndMenuIdAndMenuItemId($user->id, 13, 9)->orderBy('id', 'DESC')->first();
            if ($old_pass && $new_pass && is_numeric($old_pass->response) && strlen($old_pass->response) == 4 && is_numeric($new_pass->response) && strlen($new_pass->response) == 4) {
                if ($old_pass->response == $user->pin) {
                    $user->pin = $new_pass->response;
                    $user->save();
                    return true;
                }
                else {
                    $user->notify = 'Invalid old pin';
                    $user->save();
                    return false;
                }
            }
            else {
                $user->notify = 'Invalid input type for old or new pin';
                $user->save();
                return false;
            }


            return true;
            break;

            default :
                return true;
                break;
        }

    }


    //confirm batch
    public function confirmBatch($user, $menu)
    {
        switch ($menu->id) {
            case 9:
            //self::storeUssdResponse($user,$message);
                //continue to another menu
                $amount = ussd_response::whereUserIdAndMenuIdAndMenuItemId($user->id, 9, 17)->orderBy('id', 'DESC')->first();
                $sendto = ussd_response::whereUserIdAndMenuIdAndMenuItemId($user->id, 9, 16)->orderBy('id', 'DESC')->first();
                $response = 'Enter Four digit pin to send ' . $amount->response . ' bitcoins to ' .  $sendto->response;

                $user->session = 6;
                $user->confirm_from = $user->menu_id;
                $user->save();

                break;
            case 14:
            //self::storeUssdResponse($user,$message);
                //continue to another menu
                $response = 'Enter Four digit pin to check account balance';

                $user->session = 6;
                $user->confirm_from = $user->menu_id;
                $user->save();

                break;
            case 2:

                break;
            case 3:

                break;
            default :
                $menu_items = self::getMenuItems($user->menu_id);
                $confirmation = "Confirm " . $menu->title;
                $value = ussd_menu_items::whereMenuIdAndStep($menu->id,$user->progress)->first();
                $response = ussd_response::whereUserIdAndMenuIdAndMenuItemId($user->id, $user->menu_id, $value->id)->orderBy('id', 'DESC')->first();
                $confirmation = $confirmation . PHP_EOL . $value->confirmation_phrase . ' ' . $response->response;
                $response = $confirmation . PHP_EOL . "1. Yes" . PHP_EOL . "2. No";
                $user->session = 2;
                $user->confirm_from = $user->menu_id;
                $user->save();

                break;
        }

        return $response;
    }


    //continue USSD Menu Progress

    public function continueUssdMenuProcess($user,$message){


        $menu = ussd_menu::find($user->menu_id);
        Log::debug(['text', $message, 'menu', $menu->id]);

        //check the user menu
        switch ($menu->type) {
            case 0:
                //authentication mini app

                break;
            case 1:
                //continue to another menu
                $response = self::continueUssdMenu($user,$message,$menu);
                break;
            case 2:
                //continue to a processs
                $response = self::continueSingleProcess($user,$message,$menu);
                break;
            case 3:
                //infomation mini app
                //
                self::infoMiniApp($user,$menu);
                break;
            default :
                self::resetUser($user);
                $response = "An error occurred";
                break;
        }

        return $response;

    }

    //continuation
    public function continueSingleProcess($user,$message,$menu){
        //validate input to be numeric
        $menuItem = ussd_menu_items::whereMenuIdAndStep($menu->id,$user->progress)->first();
        $message = str_replace(",","",$message);

        switch ($menu->id) {
            default :
                self::storeUssdResponse($user,$message);
                //check if we have another step
                $step = $user->progress + 1;
                $menuItem = ussd_menu_items::whereMenuIdAndStep($menu->id,$step)->first();
                if($menuItem){

                    $user->menu_item_id = $menuItem->id;
                    $user->menu_id = $menu->id;
                    $user->progress = $step;
                    $user->save();
                    return $menuItem ->description;
                }else{
                    $response = self::confirmBatch($user,$menu);
                    return $response;

                }
                break;
        }

        return $response;
    }

    //continue USSD Menu
    public function continueUssdMenu($user,$message,$menu){
        //verify response
        $menu_items = self::getMenuItems($user->menu_id);

        $i = 1;
        $choice = "";
        $next_menu_id = 0;
        foreach ($menu_items as $key => $value) {
            if(self::validationVariations(trim($message),$i,$value->description)){
                $choice = $value->id;
                if ($value->type == 2) {
                    $next_menu_id = intval($value->menu_id) + intval($message);
                }
                else if ($value->type == 1) {
                $next_menu_id = $value->next_menu_id;
                }

                break;
            }
            $i++;
        }
        if(empty($choice)){
            //get error, we could not understand your response
            $response = "We could not understand your response". PHP_EOL;


            $i = 1;
            $response = $menu->title.PHP_EOL;
            foreach ($menu_items as $key => $value) {
                $response = $response . $i . ": " . $value->description . PHP_EOL;
                $i++;
            }

            return $response;
            //save the response
        }else{
            //there is a selected choice
            $menu = ussd_menu::find($next_menu_id);
            //next menu switch
            $response = self::nextMenuSwitch($user,$menu);
            return $response;
        }

    }

    public function nextMenuSwitch($user,$menu){

//		print_r($menu);
//		exit;
        switch ($menu->type) {
            case 0:
                //authentication mini app

                break;
            case 1:
                //continue to another menu
                $menu_items = self::getMenuItems($menu->id);
                $i = 1;
                $response = $menu->title.PHP_EOL;
                foreach ($menu_items as $key => $value) {
                    $response = $response . $i . ": " . $value->description . PHP_EOL;
                    $i++;
                }

                $user->menu_id = $menu->id;
                $user->menu_item_id = 0;
                $user->progress= 0;
                $user->save();
                //self::continueUssdMenu($user,$message,$menu);
                break;
            case 2:
               //$response = $menu->title.PHP_EOL;
               //$user->menu_id = $menu->id;
               //$user->progress= 0;
               // $user->save();
                //start a process
//				print_r($menu);
//				exit;
                self::storeUssdResponse($user,$menu);

                $response = self::singleProcess($menu,$user,1);
                //return $response;

                break;
            case 3:
                self::infoMiniApp($user,$menu);
                break;
            default :
                self::resetUser($user);
                $response = "An authentication error occurred";
                break;
        }

        return $response;

    }

    public function validationVariations($message, $option, $value)
    {
        if ((trim(strtolower($message)) == trim(strtolower($value))) || ($message == $option) || ($message == "." . $option) || ($message == $option . ".") || ($message == "," . $option) || ($message == $option . ",")) {
            return TRUE;
        } else {
            return FALSE;
        }

    }
    //store USSD response
    public function storeUssdResponse($user,$message){

        $data = ['user_id'=>$user->id,'menu_id'=>$user->menu_id,'menu_item_id'=>$user->menu_item_id,'response'=>$message];
        return ussd_response::create($data);


    }

    //single process

    public function singleProcess($menu, $user,$step) {

        $menuItem = ussd_menu_items::whereMenuIdAndStep($menu->id,$step)->first();

        if ($menuItem) {
            //update user data and next request and send back
            $user->menu_item_id = $menuItem->id;
            $user->menu_id = $menu->id;
            $user->progress = $step;
            $user->session = 1;
            $user->save();
            return $menuItem->description;

        }

    }
    public function sendResponse($response,$type=1,$user=null)
    {
        if ($type == 1) {
            $output = "CON ";


        } elseif($type == 2) {
            $output = "CON ";
            $response = $response.PHP_EOL."1. Back to main menu";
            $user->session = 4;
            $user->progress = 0;
            $user->save();
        }elseif($type == 3) {
            $output = "CON ";
            $response = $response.PHP_EOL."1. Back to main menu".PHP_EOL."2. Log out";
            $user->session = 4;
            $user->progress = 0;
            $user->save();
        }else{
            $output = "END ";
        }

        $output .= $response;
        header('Content-type: text/plain');
        echo $output;
        exit;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMenuAndItems($user,$menu_id){
        //get main menu
        if ($user) {
        $user->menu_id = $menu_id;
        $user->session = 1;
        $user->progress = 1;
        $user->save();
        }
        //get home menu
        $menu =  ussd_menu::find($menu_id);

        $menu_items = self::getMenuItems($menu_id);


        $i = 1;
        $response = $menu->title.PHP_EOL;
        foreach ($menu_items as $key => $value) {
            $response = $response . $i . ": " . $value->description . PHP_EOL;
            $i++;
        }
        return $response;
    }

    //Menu Items Function
    public static function getMenuItems($menu_id)
    {
        $menu_items = ussd_menu_items::whereMenuId($menu_id)->get();
        return $menu_items;
    }

    public function resetUser($user)
    {
        $user->session = 0;
        $user->progress = 0;
        $user->menu_id = 0;
        $user->difficulty_level = 0;
        $user->confirm_from = 0;
        $user->menu_item_id = 0;

        return $user->save();

    }

    public function user_is_starting($text)
    {
        if (strlen($text) > 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

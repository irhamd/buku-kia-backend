<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class NotifikasiController extends Controller
{
   
   
   static public function sendNotification(  $title, $body, $token)
    {        
        define('FCM_AUTH_KEY', 'AAAAmQCdtdI:APA91bF9G7kjmctGKEEE83nGP48bWn6L8peFDCRVhUQa71tGvZra19GkhtQA9LnbcBMPrwe54NhUgDkLPWrm7_8ykAX9NCnkuf-Mc1I_qDZU2AhzqJVQ9TcJ6LwEZDSbFPpa2Rcg2OoQ');
	    $postdata = json_encode(
	    [
	        'notification' => 
	        	[
	        		'title' => $title,
	        		'body' => $body,
	        		'icon' => '',
	        		'click_action' => ""
	        	]
	        ,
	        'to' => $token
	    ]
	);

	$opts = array('http' =>
	    array(
	        'method'  => 'POST',
	        'header'  => 'Content-type: application/json'."\r\n"
	        			.'Authorization: key='.FCM_AUTH_KEY."\r\n",
	        'content' => $postdata
	    )
	);

	$context  = stream_context_create($opts);

	$status = file_get_contents('https://fcm.googleapis.com/fcm/send', false, $context);
 
    return response()->json([
        "sts" =>json_decode($status)
    ]);
    }

   

}

<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class NotifikasiController extends Controller
{
   
   
   static public function sendNotification( Request $req ){
		$ServerKey = 'AAAAmQCdtdI:APA91bF9G7kjmctGKEEE83nGP48bWn6L8peFDCRVhUQa71tGvZra19GkhtQA9LnbcBMPrwe54NhUgDkLPWrm7_8ykAX9NCnkuf-Mc1I_qDZU2AhzqJVQ9TcJ6LwEZDSbFPpa2Rcg2OoQ';
			$postdata = json_encode([
					'notification' => [
						'title' => $req->title,
						'body' => $req->body,
						'icon' => 'https://cdn-icons-png.flaticon.com/512/891/891012.png',
						'click_action' => ""
					],'to' => $req->token
				]);
			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/json'."\r\n"
								.'Authorization: key='.$ServerKey."\r\n",
					'content' => $postdata
				)
			);

			$context  = stream_context_create($opts);

			$result = file_get_contents('https://fcm.googleapis.com/fcm/send', false, $context);
			if($result) {
				return "1";
			} else 
				
			return "0";
		
	}




   

}

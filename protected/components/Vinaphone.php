<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class Vinaphone extends CComponent {

    public static function charging($transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $content = "", $channel = CHANNEL_TYPE_WAP) {
        $vcgw = new VCGW();
        return $vcgw->debitAccount($transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $content, $channel);
    }

    public static function charging2($user_name, $user_ip, $transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $trial, $bundle, $content = "", $channel = CHANNEL_TYPE_WAP) {
        $vcgw = new VCGW();
        return $vcgw->debitAccount2($user_name, $user_ip, $transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $trial, $bundle, $content, $channel);
    }

    public function actionTestCharging($msisdn, $amount, $command, $content) {
        header("Content-type: text/plain");
        $vcgw = new VCGW();
        $result = $vcgw->debitAccount(time(), $msisdn, false, $amount, $amount, $command, $content);
        if ($result != null) {
            echo("charge returned code: $result->return \n");
            echo("charge returned message: $result->error_desc \n");
        } else {
            echo("Charge fail");
        }
        $date = date('Y-m-d H:i :s');
        echo("time: $date\n");
    }
    public static function errorCharging2Message($errorCode){
		$string = "";
		switch($errorCode){
			case NOK_NO_MORE_CREDIT_AVAILABLE:
				$string = "Thue bao khong du tien.";
				break;
			case NOK_CPS_INVALID_REQUEST_PARAMETERS:
				$string = "Loi he thong.";
				break;
			case NOK_VAS_REQUEST_FAIL:
				$string = "Loi he thong";
				break;
			case NOK_SUBSCRIBER_NOT_IN_BILLING_SYSTEM:
				$string = "Thue bao khong co kha nang thanh toan";
				break;
			case NOK_SUBSCRIBER_TWO_WAY_BLOCKED:
				$string = "Thue bao dang bi khoa hai chieu";
				break;
			case NOK_CPS_GENERAL_ERROR:
				$string = "Loi he thong";
				break;
			case NOK_SUBSCRIBER_MSISDN_CHANGE_OWNER:
				$string = "Thue bao da thoi doi nguoi dung";
				break;
			case NOK_SUBSCRIBER_NOT_REGISTERED_SERVICE:
				$string = "Thue bao chua dang ky dich vu";
				break;
			default:
				$string = "Loi he thong";
		}
		return $string;
	}
        public static function sendSms($msisdn, $content,$save = true)
	{
		$subscriber = $msisdn == '' ? null : Subscriber::model()->findByAttributes(array('subscriber_number' => $msisdn));
		Yii::log("Send message to $msisdn: ".$content);
		
		$aDate = new DateTime();

		$serviceNumber = Yii::app()->params['serviceNumber'];
		$smsGateway = Yii::app()->params['smsGateway'];
		$url = sprintf($smsGateway, urlencode($subscriber->subscriber_number), urlencode($content));

		//error_log("send sms url: $url\n");
		$curl = new MyCurl();
		$response = $curl->get($url);
		
		if($save){
			$mt = new SmsMessage();
			$mt->type = 'MT';
			$mt->source = SERVICE_PHONE_NUMBER;
			$mt->destination = $msisdn;
			$mt->message = $content;
			$mt->received_time = $aDate->format('Y-m-d H:i:s');
			$mt->sending_time = $aDate->format('Y-m-d H:i:s');
			if(isset($response) && isset($response->body)){
				$mt->mt_status = $response->body;
			}
			if (isset($subscriber))
				$mt->subscriber_id = $subscriber->id;
			$mt->save();
		}
		
		return $mt;
	}
}

<?php

class VinaphoneController extends Controller
{
	const DATASYNC_NO_ERROR = 0;
	const DATASYNC_ERR_INVALID_DATA = 7001;
	const DATASYNC_ERR_INTERNAL = 7999;
	const DATASYNC_ERR_SUBS_EXISTS = 7201;
	const DATASYNC_ERR_SUBS_NOT_EXISTS = 7219;
	const DATASYNC_ERR_PRODUCT_NOT_EXISTS = 7203;
	const DATASYNC_ERR_SERVICE_NOT_OPEN = 7209;
	const DATASYNC_ERR_UNKNOWN = "#";
	
	const SUSCRIPTION_RESULT_REGISTER_SUCCESS = 0;
	const SUSCRIPTION_RESULT_CANCEL_SUCCESS = 1;
	const SUSCRIPTION_RESULT_EXISTED = 2;
	const SUSCRIPTION_RESULT_NOT_AVAILABLE = 3;
	const SUSCRIPTION_RESULT_EXCEPTION = 4;
        
	const VNP_SUBSCRIPTION_TYPE_REGISTER = "1"; // REGISTER
	const VNP_SUBSCRIPTION_TYPE_CANCEL = "2"; // CANCEL
	
	public static $dataSyncErrMsg = array(
			self::DATASYNC_NO_ERROR => "Success",
			self::DATASYNC_ERR_INVALID_DATA => "Field value is invalid",
			self::DATASYNC_ERR_INTERNAL => "System internal error",
			self::DATASYNC_ERR_SUBS_EXISTS => "The subscription already exists",
			self::DATASYNC_ERR_SUBS_NOT_EXISTS => "The subscription does not exist",
			self::DATASYNC_ERR_PRODUCT_NOT_EXISTS => "The product does not exist",
			self::DATASYNC_ERR_SERVICE_NOT_OPEN => "The service is not open",
			self::DATASYNC_ERR_UNKNOWN => "Error",
	);
	
	
	/**
	 * 
	 * @param String $clientIp
	 * @return ResultResponse
	 */
	public static function detectMsisdn() {
		$x_ipaddress = isset($_SERVER['HTTP_X_IPADDRESS'])?$_SERVER['HTTP_X_IPADDRESS']:null; 
		$x_forwarded = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:null;
		$x_wapmsisdn = isset($_SERVER['HTTP_X_WAP_MSISDN'])?$_SERVER['HTTP_X_WAP_MSISDN']:null;
		$x_userip = isset($_SERVER['HTTP_USERIP'])?$_SERVER['HTTP_USERIP']:null;
		$remote_ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:null;
		$vinaid_url = Yii::app()->params['VINAID_URL'];
		$ch = new MyCurl();
		$response = $ch->get($vinaid_url, array(
				'msisdn' => null,
				'xipaddress' => $x_ipaddress,
				'xforwarded' => $x_forwarded,
				'xwapmsisdn' => $x_wapmsisdn,
				'userip' => $x_userip,
				'remoteip' => $remote_ip,
				'service' => "VFILM",
		));
		
		
		if ($response == null || $response === false){
			//Yii::log("Detect MSISDN RESPONSE: NULL");
			return "";
		}else{
			//Yii::log("Detect MSISDN RESPONSE: " . $response);
			$arr_res = explode('|', $response);
			$msisdn = (count($arr_res) >= 3)?$arr_res[1]:"";
			return $msisdn;
		}
	}
	
	public function actionMsisdn()
	{
// 		header("Content-type: text/plain");
		$res = self::detectMsisdn();
		echo("Code: " . $res . "\n<p/>");
	}
	
/*	public function actionTestMo($username, $password, $source, $dest, $content)
	{
		$soapClient = new SoapClient("http://thuc.com/ifilm/molistener/wsdl");
		$response = $soapClient->moRequest($username, $password, $source, $dest, $content);
		
		echo $response;
	}*/
	
	/**
	 * @return ActiveRecord table SMSMessage
	 */
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
	
	/**
	 * http://210.211.99.188:8300/smsws?wsdl
	 * http://10.58.128.105:8300/smsws?wsdl
	 */
	/*public function actionSendSms($msisdn, $content)
	{
		$res = self::sendSms($msisdn, $content);
		echo 'Res: ' . $res->mt_status;
	}*/

	/**
	 * http://210.211.99.188:20010/process/services/ProcessClients?wsdl
	 * http://10.58.46.150:20010/process/services/ProcessClients?wsdl
	 */
	/* public function actionCharging($msisdn, $amount, $command, $content)
	{
		$response = self::charging($msisdn, $amount, $command,$content);
		echo("charge returned code: " . $response->return);
	} */
	
	/**
	 * 
	 * @param unknown $orderId
	 * @param unknown $orderInfo
	 * @param unknown $orderDate - 14 ky tu "yyyyMMdHHmmss"
	 * @param unknown $price
	 * @param unknown $originPrice
	 * @param unknown $isPromotion - 0/1
	 * @param unknown $returnUrl
	 * @param unknown $backUrl
	 */
	public static function makeCheckoutUrl($orderId, $orderInfo, $orderDate, $price, $originPrice,
			$isPromotion, $returnUrl, $backUrl) {
		$orderInfo = urlencode($orderInfo);
		$returnUrl = urlencode($returnUrl);
		$backUrl = urlencode($backUrl);
		$secureCode = md5($orderId." ".$orderDate." ".self::VNP_SECURE_PASS);
		$url = "http://wapgate.Viettel.com.vn/checkout.jsp?".
		"orderid=$orderId&orderinfo=$orderInfo&orderdatetime=$orderDate".
		"&price=$price&reason=".self::VNP_CHECKOUT_PARAM_REASON."&originalprice=$originPrice".
		"&promotion=$isPromotion&note=&returnurl=$returnUrl".
		"&backurl=$backUrl&servicename=".self::VNP_SERVICE_NAME.
		"&securecode=$secureCode&language=vi";
		
		return  $url;
	}
	
	/**
	 * 
	 * @param unknown $requestId
	 * @param unknown $subInfo
	 * @param unknown $requestDate
	 * @param unknown $packageID - ma goi cuoc, 14 ky tu, do VNP khai bao
	 * @param unknown $subType - 1 ==> dang ky, 2 ==> huy
	 * @param unknown $returnUrl
	 * @param unknown $backUrl
	 * @return string
	 */
	public static function makeSubscriptionUrl($requestId, $subInfo, $requestDate, 
			$packageID, $subType, $returnUrl, $backUrl) {
		$subInfo = urlencode($subInfo);
		$returnUrl = urlencode($returnUrl);
		$backUrl = urlencode($backUrl);
		$secureCode = md5($requestId." ".$requestDate." ".self::VNP_SECURE_PASS);
		$url = "http://wapgate.Viettel.com.vn/subscription.jsp?".
				"requestid=$requestId&subscriptioninfo=$subInfo&requestdatetime=$requestDate".
				"&subscriptionid=$packageID&subscriptiontype=$subType&returnurl=$returnUrl".
				"&backurl=$backUrl&servicename=".self::VNP_SERVICE_NAME.
				"&securecode=$secureCode&language=vi";
	
		return  $url;
	}
	
	public static function verifyCheckoutReturn($order_id, $checkout_datetime, $transaction_id, $return_secure_code){
		$secure_code = md5($order_id." ".$checkout_datetime." ".$transaction_id." ".self::VNP_SECURE_PASS);
		if($return_secure_code == $secure_code){
			return true;
		}else{
			return false;
		}
	}
	public static function verifySubscribeReturn($request_id, $subscribe_datetime, $result, $return_secure_code){
		$secure_code = md5($request_id." ".$subscribe_datetime." ".$result." ".self::VNP_SECURE_PASS);
		if($return_secure_code == $secure_code){
			return true;
		}else{
			return false;
		}
	}
	public function actionShowHeader()
	{
		header("Content-type: text/plain");
		var_dump($_SERVER);
	}
	
	public function actionCheckIP(){
		$ipRanges = Yii::app()->params['ipRanges'];
		$clientIP = Yii::app()->request->getUserHostAddress();
		foreach ($ipRanges as $range) {
			if (CUtils::cidrMatch($clientIP, $range)) {
				$found = true;
				break;
			}
		}
		header("Content-type: text/plain");
		echo $found;
	}
	
	
	/**
	 * backUrl c???a CheckoutCommit tr??? v??? ????y
	 */
	public function actionCheckoutCancel()
	{
		
	}
	
	public function actionIndex()
	{
		header("Content-type: text/plain");
		echo "index\nline 2";
	}
	public function isValidNotif($eventXML) {
		if ($eventXML != NULL && $eventXML->msisdn != NULL && strlen($eventXML->msisdn) > 0 
		&& $eventXML->action=="PAYMENT") {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function isValidMsisdn($msisdn) {
		if ($msisdn != NULL && strlen($msisdn) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/* public function actionDataSyncWS() {
		// 		echo "POST data:\n";
		$postdata = file_get_contents("php://input"); // lay raw POST data
		Yii::log('post data: '.$postdata, 'error');
		/* $postdata = "<?xml version='1.0' encoding='UTF-8'?>
				<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">
					<soapenv:Body>
						<ns2:syncOrderRelation xmlns:ns2=\"http://www.csapi.org/schema/parlayx/data/sync/v1_0/local\">
							<ns2:userID> 
								<ID>841238500017</ID>
								<type>0</type> 
							</ns2:userID>
							<ns2:spID>003505</ns2:spID> 
							<ns2:productID>MDSP2000090233</ns2:productID> 
							<ns2:serviceID>0035052000003316</ns2:serviceID> 
							<ns2:serviceList>0035052000003316</ns2:serviceList> 
							<ns2:updateType>2</ns2:updateType> 
							<ns2:updateTime>20120920083245</ns2:updateTime> 
							<ns2:updateDesc>Deletion</ns2:updateDesc> 
							<ns2:effectiveTime>20120904065243</ns2:effectiveTime> 
							<ns2:expiryTime>20121003170000</ns2:expiryTime> 
							<ns2:extensionInfo>
								<namedParameters> 
									<key>productOrderKey</key>
									<value>999000000000167376</value> 
								</namedParameters>
							</ns2:extensionInfo> 
						</ns2:syncOrderRelation>
					</soapenv:Body> 
				</soapenv:Envelope>"; **/
		/*
		try {
// 			$dataXML = simplexml_load_string($postdata);
			$dataXML = new SimpleXMLElement($postdata);
		}
		catch (Exception $e) {
                    Yii::log("Data XML NULL", "error");
                    $error = self::DATASYNC_ERR_INVALID_DATA;
                    $dataXML = NULL;
		}
		
		echo header("Content-type: text/xml");
		$subscriberOrder = NULL;
		if ($dataXML != NULL) {
                    $error = self::DATASYNC_NO_ERROR;
			try {
				$namespaces = $dataXML->getNamespaces(true);
	// 			var_dump($namespaces);
				//array(2) {
				//   ["soapenv"]=>
				//   string(41) "http://schemas.xmlsoap.org/soap/envelope/"
				//   ["ns2"]=>
				//   string(56) "http://www.csapi.org/schema/parlayx/data/sync/v1_0/local"
				// }
				$i = 0;
				foreach ($namespaces as $key=>$value) {
					$ns[$i++] = $key;
				}
	// 			$syncOrderRelation = $dataXML->Envelope->Body->syncOrderRelation;
	// 			$msisdn = $syncOrderRelation->userID->ID;
	// 			$syncOrderRelation = $dataXML->{'soapenv:Body'}->{'ns2:syncOrderRelation'};
	// 			var_dump($dataXML);
				$syncOrderRelation = $dataXML->children($ns[0], true)->Body->children($ns[1], true);
	// 			var_dump($syncOrderRelation);
				$syncNodes = $syncOrderRelation->children($ns[1], true);
				$msisdn = $syncNodes->userID->children()->ID;
	// 			var_dump($msisdn);
				$spID = $syncNodes->spID;
				$productID = $syncNodes->productID;
				$serviceID = $syncNodes->serviceID;
				$serviceList = $syncNodes->serviceList;
				$updateType = $syncNodes->updateType;
				$updateTime = $syncNodes->updateTime;
				$updateDesc = $syncNodes->updateDesc;
				$effectiveTime = $syncNodes->effectiveTime;
				$expiryTime = $syncNodes->expiryTime;
				$extensionInfo = $syncNodes->extensionInfo;
				$productOrderKey=NULL;
	
				$service = Service::model()->findByAttributes(array('content_id' => $productID));
				if($service == NULL) {
					$error = self::DATASYNC_ERR_PRODUCT_NOT_EXISTS;
					throw new Exception('not exists product');
				}
				foreach ($extensionInfo->children() as $node) {
	// 				echo "node: ";
	// 				var_dump($node);
					if ($node->children()->key == "productOrderKey") {
						$productOrderKey = $node->children()->value;
					}
				}
				
				if (!$this->isValidMsisdn($msisdn)) {
					$error = self::DATASYNC_ERR_INVALID_DATA;
					throw new Exception('invalid msisdn');
				}
				
				$subscriber = Subscriber::model()->findByAttributes(array('subscriber_number' => $msisdn));
				if($subscriber == NULL) { //xu ly cua truong hop SMS
					$subscriber = Subscriber::newSubscriber($msisdn);
					if($subscriber == NULL) {
						$error = self::DATASYNC_ERR_INTERNAL;
						throw new Exception('internal error');
					}
				}
				if($updateType == 1) { //dang ky dich vu
					if($subscriber->isUsingService()) {
						$error = self::DATASYNC_ERR_SUBS_EXISTS;
						throw new Exception("subscriber $msisdn had registered service");
					}
					$subscriber->inactiveOtherServices(); 
		// 			$subscriberService = $subscriber->addService($service,$ref_id);
					$subscriberService = $subscriber->addService($service);
					if($subscriberService != NULL) {
						$subscriberService->product_order_key = $productOrderKey;
						$subscriberService->update();
					}else{
						$error = self::DATASYNC_ERR_INTERNAL;
						throw new Exception('update service subscriber mapping failed');
					}
                                        
                                        $subscriberOrder = $subscriber->newOrder(CHANNEL_TYPE_WAP, USING_TYPE_REGISTER, PURCHASE_TYPE_NEW, $service, null, 1, $productOrderKey);
				}
				else if($updateType == 2) { // huy dich vu
					$smap = ServiceSubscriberMapping::model()->findByAttributes(array('subscriber_id'=>$subscriber->id, 'is_active'=>1, 'service_id'=>$service->id));
					if($smap != NULL) {
						$smap->is_active = 0;
						$smap->is_deleted = 1;
						$smap->modify_date = new CDbExpression('NOW()');
						$smap->update();
					}
					else {
						$error = self::DATASYNC_ERR_SUBS_NOT_EXISTS;
						throw new Exception('cancel service failed. SSM is not existed');
					}
                                        $subscriberOrder = $subscriber->newOrder(CHANNEL_TYPE_WAP, 0, PURCHASE_TYPE_CANCEL, $service, null, 1, $productOrderKey);
				}
				$data_recei = "msisdn: $msisdn, spID: $spID, productID: $productID, serviceID: $serviceID, "
					."serviceList: $serviceList, updateType: $updateType, updateTime: $updateTime, "
					."updateDesc: $updateDesc, effectiveTime: $effectiveTime, expiryTime: $expiryTime, "
					."productOrderKey: $productOrderKey";
				Yii::log('Recive data: '.$data_recei, 'error');
			}
			catch (Exception $e) {
                            if($subscriberOrder != NULL) {
                                $subscriberOrder->status = 2;
                                $subscriberOrder->error_code = "ERROR";
                                $subscriberOrder->update();
				Yii::log($e->getMessage(), 'error');
                            }
			}
		}
		
		$response = "<?xml version='1.0' encoding='UTF-8'?>
				<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://www.csapi.org/schema/parlayx/data/sync/v1_0/local\">
					<soapenv:Header/> 
					<soapenv:Body>
						<loc:syncOrderRelationResponse> 
							<loc:result>$error</loc:result>
							<loc:resultDescription>".self::$dataSyncErrMsg["$error"]."</loc:resultDescription> 
						</loc:syncOrderRelationResponse>
					</soapenv:Body> 
				</soapenv:Envelope>";
		Yii::log('Response data: '.$response, 'error');
 		echo $response;
	} */

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
	
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

	/**
	 * http://210.211.99.188:20010/process/services/ProcessClients?wsdl
	 * http://10.58.46.150:20010/process/services/ProcessClients?wsdl
	 */
	public static function charging($transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $content="", $channel=CHANNEL_TYPE_WAP)
	{
		$vcgw = new VCGW();
		return $vcgw->debitAccount($transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $content, $channel);
	}

    public static function charging2($user_name, $user_ip, $transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $trial, $bundle, $content="", $channel=CHANNEL_TYPE_WAP)
    {
        $vcgw = new VCGW();
        return $vcgw->debitAccount2($user_name, $user_ip, $transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $trial, $bundle, $content, $channel);
    }

	public function actionTestCharging($msisdn, $amount, $command, $content)
	{
		header("Content-type: text/plain");
		$vcgw = new VCGW();
		$result = $vcgw->debitAccount(time(), $msisdn, false, $amount, $amount, $command, $content);
		if($result != null){
			echo("charge returned code: $result->return \n");
			echo("charge returned message: $result->error_desc \n");
		}else{
			echo("Charge fail");
		}
		$date = date('Y-m-d H:i :s');
		echo("time: $date\n");
	}
}

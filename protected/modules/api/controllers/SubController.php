<?php

class SubController extends ApiController {

	protected $pageSize = 10;
	protected $haveCookie = true;

	public function filters() {
		return array(
// 				'3GOnly + subscribe, cancel',
// 				'HaveCookie + login'
		);
	}

	public function filter3GOnly($filterChain) {
		if ($this->accessType == Controller::$ACCESS_VIA_3G) {
			$filterChain->run();
		} else {
			Yii::app()->user->setFlash('responseToUser', "Quí khách truy cập 3G của VinaPhone mới sử dụng được tính năng này!");
			$this->redirect(Yii::app()->homeUrl);
		}
	}

	public function actionGetTransactions() {
		$fDate = isset($_REQUEST['from_date']) ? DateTime::createFromFormat('d/m/Y H:i:s', $_REQUEST['from_date'] . " 00:00:00") : null;
		if ($fDate == null || $fDate == FALSE)
			$fDate = DateTime::createFromFormat('d/m/Y H:i:s', date('01/m/Y 00:00:00'));

		$tDate = isset($_REQUEST['to_date']) ? DateTime::createFromFormat('d/m/Y H:i:s', $_REQUEST['to_date'] . " 23:59:59") : null;
		if ($tDate == null || $tDate == FALSE)
			$tDate = DateTime::createFromFormat('d/m/Y H:i:s', date('d/m/Y 23:59:59'));

		$page = isset($_REQUEST['page'])?$_REQUEST['page']:0;
		$page_size = isset($_REQUEST['page_size'])?$_REQUEST['page_size']:10;

		if($this->subscriber == NULL) {
			$this->responseError(1, 1, "null subscriber");
		}

		$res = $this->subscriber->getSubscriberTransactionsHistory(
				$fDate->format('Y-m-d H:i:s'), $tDate->format('Y-m-d H:i:s'), $page, $page_size);

		header("Content-type: text/xml; charset=utf-8");
		$xmlDoc = new DOMDocument();
		$xmlDoc->encoding = "UTF-8";
		$xmlDoc->version = "1.0";

		//TODO: authen, session, error handle
		$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
		$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
		$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
		$root->appendChild($xmlDoc->createElement("action", $this->action->id));
		$root->appendChild($xmlDoc->createElement("error_no", "0"));
		$result = $root->appendChild($xmlDoc->createElement("result"));

		$result->appendChild($xmlDoc->createElement('page_number', CHtml::encode($res['page_number'])));
		$result->appendChild($xmlDoc->createElement('page_size', CHtml::encode($res['page_size'])));
		$result->appendChild($xmlDoc->createElement('total_page', CHtml::encode($res['total_page'])));
		$result->appendChild($xmlDoc->createElement('total_result', CHtml::encode($res['total_result'])));

		foreach($res['data'] as $transaction) {
			$transactionNode = $xmlDoc->createElement('transaction');
			$sType = SubscriberTransaction::getTypeName($transaction['purchase_type']);
			$transactionNode->appendChild($xmlDoc->createElement("type", $sType));
			$transactionNode->appendChild($xmlDoc->createElement("description", $transaction['description']));
			$transactionNode->appendChild($xmlDoc->createElement("create_date", $transaction['create_date']));
			$sCost = intval($transaction['cost'])."đ";
			$transactionNode->appendChild($xmlDoc->createElement("cost", $sCost));
			$result->appendChild($transactionNode);
		}

		$xmlDoc->formatOutput = true;
		$content = $xmlDoc->saveXML();
		echo $content;
	}

	public function actionPayService() {
// 		$service_type = isset($_REQUEST['service_type']) ? $_REQUEST['service_type'] : 0;
// 		$service = Service::model()->findByAttributes(array('code_name' => $service_type));
		$service = Service::model()->findByAttributes(array('is_active' => 1));
		if($service == NULL) {
			$this->responseError(1,1, "Service $service_type is not existed");
		}

		if($this->subscriber == NULL) {
			$this->responseError(1,1, "Không nhận diện được thuê bao");
		}
        $user_name = NULL;
        $user_ip = NULL;

		$registerStatus = ChargingProxy_test::processSubService(CHANNEL_TYPE_APP, ChargingProxy_test::ACTION_REGISTER, $service, $this->subscriber, NULL, $user_name, $user_ip);
		if($registerStatus == ChargingProxy_test::PROCESS_ERROR_NONE) {
			$this->responseError(0, 0, "Đăng ký dịch vụ thành công");
		}
		else {
			$this->responseError($registerStatus, $registerStatus, ChargingProxy_test::getNameErrorCode($registerStatus));
		}
	}

	public function actionCancelService() {
// 		$service_type = isset($_REQUEST['service_type']) ? $_REQUEST['service_type'] : 0;
// 		$service = Service::model()->findByAttributes(array('code_name' => $service_type));
		$service = Service::model()->findByAttributes(array('is_active' => 1));
		if($service == NULL) {
			$this->responseError(1,1, "Service $service_type is not existed");
		}

		if($this->subscriber == NULL) {
			$this->responseError(1,1, "Không nhận diện được thuê bao");
		}
        $user_name = NULL;
        $user_ip = NULL;

		$cancelStatus = ChargingProxy_test::processSubService(CHANNEL_TYPE_APP, ChargingProxy_test::ACTION_CANCEL, $service, $this->subscriber, NULL, $user_name, $user_ip);
		if($cancelStatus == ChargingProxy::PROCESS_ERROR_NONE) {
			$this->responseError(0, 0, "Hủy dịch vụ thành công");
		}
		else {
			$this->responseError($cancelStatus, $cancelStatus, ChargingProxy_test::getNameErrorCode($cancelStatus));
		}
	}

	public function actionGetAccountInfo() {
		if($this->subscriber == NULL) {
			$this->responseError(1,1, "Không nhận diện được thuê bao");
		}

		header("Content-type: text/xml; charset=utf-8");
		$xmlDoc = new DOMDocument();
		$xmlDoc->encoding = "UTF-8";
		$xmlDoc->version = "1.0";

		//TODO: authen, session, error handle
		$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
		$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
		$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
		$root->appendChild($xmlDoc->createElement("action", $this->action->id));
		$root->appendChild($xmlDoc->createElement("error_no", "0"));
		$result = $root->appendChild($xmlDoc->createElement("result"));

		$usingServiceCodeName = "";
		$usingServiceExpiryDate = "";
		if(count($this->usingServices) > 0) {
			$usingServiceCodeName = $this->usingServices[0]->service->code_name;
			$usingServiceExpiryDate = $this->usingServices[0]->expiry_date;
		}
		$result->appendChild($xmlDoc->createElement("current_service", $usingServiceCodeName));
		$result->appendChild($xmlDoc->createElement("expiry_date", $usingServiceExpiryDate));
		$emailNode = $result->appendChild($xmlDoc->createElement("email", $this->subscriber->email));
		$emailStatus = (isset($this->subscriber->verification_code) && $this->subscriber->verification_code != 0)?0:1;
		$emailNode->appendChild($xmlDoc->createAttribute("email_status"))
		->appendChild($xmlDoc->createTextNode($emailStatus));
		$wifiUsing = ($this->accessType == self::$ACCESS_VIA_WIFI)?1:0;
		$result->appendChild($xmlDoc->createElement("wifi_using", $wifiUsing));

		$xmlDoc->formatOutput = true;
		$content = $xmlDoc->saveXML();
		echo $content;
	}

	public function actionGetServices() {
		header("Content-type: text/xml; charset=utf-8");
		$xmlDoc = new DOMDocument();
		$xmlDoc->encoding = "UTF-8";
		$xmlDoc->version = "1.0";

		//TODO: authen, session, error handle
		$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
		$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
		$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
		$root->appendChild($xmlDoc->createElement("action", $this->action->id));
		$root->appendChild($xmlDoc->createElement("error_no", "0"));
		$result = $root->appendChild($xmlDoc->createElement("result"));

		$arrServicesNode = $result->appendChild($xmlDoc->createElement("services"));
		$arrServices = Service::model()->findAllByAttributes(array("is_active" => 1));
		foreach($arrServices as $service) {
// 			$service = new Service; //fixme xoa dong nay
			$serviceNode = $arrServicesNode->appendChild($xmlDoc->createElement("service"));
			$serviceNode->appendChild($xmlDoc->createAttribute("create_date"))
			->appendChild($xmlDoc->createTextNode($service->create_date));
			$serviceNode->appendChild($xmlDoc->createAttribute("modify_date"))
			->appendChild($xmlDoc->createTextNode($service->modify_date));
			$serviceNode->appendChild($xmlDoc->createElement("display_name", $service->display_name));
			$serviceNode->appendChild($xmlDoc->createElement("code_name", $service->code_name));
			$serviceNode->appendChild($xmlDoc->createElement("price", intVal($service->price)));
			$serviceNode->appendChild($xmlDoc->createElement("duration", $service->using_days));
			$serviceNode->appendChild($xmlDoc->createElement("description", $service->description));
			$serviceNode->appendChild($xmlDoc->createElement("freeDownloadCount", $service->free_download_count));
			$serviceNode->appendChild($xmlDoc->createElement("freeViewCount", $service->free_view_count));
			$serviceNode->appendChild($xmlDoc->createElement("freeGiftCount", $service->free_gift_count));
		}
		$xmlDoc->formatOutput = true;
		$content = $xmlDoc->saveXML();
		echo $content;
	}
	
	public function actionGetLastVersion() {
		$platform = isset($_REQUEST['device_type_id']) ? $_REQUEST['device_type_id'] : 1;
		$currentVersion = isset($_REQUEST['current_version']) ? $_REQUEST['current_version'] : 0;
		
		$currentAppVersion = AppVersionPlatform::model()->findBySql("select * from app_version_platform where app_version_code = $currentVersion and platform = $platform order by app_version_code desc limit 1");
                
		$status = 1;
		if($currentAppVersion != NULL) {
			$status = $currentAppVersion->status; //under review
		}
		
		header("Content-type: text/xml; charset=utf-8");
		$xmlDoc = new DOMDocument();
		$xmlDoc->encoding = "UTF-8";
		$xmlDoc->version = "1.0";
		
		//TODO: authen, session, error handle
		$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
		$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
		$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
		$root->appendChild($xmlDoc->createElement("action", $this->action->id));
		$result = $root->appendChild($xmlDoc->createElement("result"));
		
		$lastAppVersion = AppVersionPlatform::model()->findBySql("select * from app_version_platform where status = 1 and app_version_code > $currentVersion and platform = $platform order by app_version_code desc limit 1");
		/* @var $lastAppVersion AppVersionPlatform */
		if($lastAppVersion == NULL) {
			$this->responseVersionError(0,0, "Ung dung da duoc cap nhat ban moi nhat", $status);
		}
		$result->appendChild($xmlDoc->createElement("app_version_code", $lastAppVersion->app_version_code));
		$result->appendChild($xmlDoc->createElement("new_version_name", $lastAppVersion->app_version_name));
		$result->appendChild($xmlDoc->createElement("download_url", CHtml::encode($lastAppVersion->download_url)));
		$result->appendChild($xmlDoc->createElement("release_date", $lastAppVersion->release_date));
		$result->appendChild($xmlDoc->createElement("status", $lastAppVersion->status));
		
		$xmlDoc->formatOutput = true;
		$content = $xmlDoc->saveXML();
		echo $content;
	}
	
	/**
	 *
	 * @param type $error_no
	 * @param type $error_code
	 * @param type $message
	 */
	public function responseVersionError($error_no, $error_code, $message, $status) {
		header("Content-type: text/xml; charset=utf-8");
		$xmlDoc = new DOMDocument();
		$xmlDoc->encoding = "UTF-8";
		$xmlDoc->version = "1.0";
	
		//TODO: authen, session, error handle
		$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
		$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
		$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
		$root->appendChild($xmlDoc->createElement("action", $this->action->id));
		$root->appendChild($xmlDoc->createElement("error_no", $error_no));
		$root->appendChild($xmlDoc->createElement("error_code", $error_code));
		$root->appendChild($xmlDoc->createElement("error_message", CHtml::encode($message)));
		$root->appendChild($xmlDoc->createElement("status", CHtml::encode($status)));
		$root->appendChild($xmlDoc->createElement("version_testing", "0"));
		echo $xmlDoc->saveXML();
		Yii::app()->end();
	}
	
	public function actionSendService() {
		if($this->subscriber == NULL || $this->accessType != Controller::$ACCESS_VIA_WIFI) {
			$this->responseError(1,1, "Vui lòng sử dụng 3G của VinaPhone để thực hiện giao dịch này.");
		}
		
		$serviceType = isset($_REQUEST['service_type']) ? $_REQUEST['service_type'] : "-1";
		$receiverNumber = isset($_REQUEST['receiver']) ? $_REQUEST['receiver'] : "-1";
		$receiverNumber = CUtils::validatorMobile($receiverNumber);
		if($receiverNumber == '') {
			$this->responseError(1,1, "Số điện thoại người nhận không hợp lệ");
		}
		
		$receiver = Subscriber::newSubscriber($receiverNumber);
		$service = Service::model()->findByAttributes(array('is_active' => 1));
		if($service == NULL) {
			$this->responseError(1,1, "Service $serviceType is not existed");
		}
		
		$giftConfirm = SubscriberGiftConfirmation::newGiftConfirm($this->subscriber, $receiver, $service);
		if($giftConfirm == NULL) {
			$this->responseError(1,1, "Xảy ra lỗi trong quá trình tặng gói $serviceType cho thuê bao $receiverNumber. Xin quý khách vui lòng thử lại sau");
		}
		
		$content = "Quy khach duoc tang goi " . $service->code_name . " xem mien phi dich vu phim truc tuyen vFilm cua MobiFone tu so may " . $this->subscriber->subscriber_number . ". Ban hay nhan tin \"CO ". $giftConfirm->confirmation_code . " \"gui 9033 de nhan qua. Truy cap http://vfilm.vn de su dung dich vu";
		$mt = VinaphoneController::sendSms($receiver->subscriber_number, $content);
		if($mt->mt_status == 0) {
			$this->responseError(1,1, "Xảy ra lỗi trong quá trình tang goi dich vu $serviceType cho thuê bao $receiverNumber. Xin quý khách vui lòng thử lại sau");	
		}
		
		$strContent = "Đã gửi tặng dịch vụ. Bạn vui lòng đợi xác nhận của thuê bao " . $receiver->subscriber_number;
		$this->responseError(0, 0, $strContent);
	}
	
	public function actionGetWifiPassword() {
		if($this->accessType == Controller::$ACCESS_VIA_WIFI) {
			if(($this->wifi_number != -1) && ($this->subscriber == NULL)) {
				$this->msisdn = CUtils::validatorMobile($this->wifi_number);
				$this->subscriber = Subscriber::newSubscriber($this->msisdn);
			}
		}
		
		if($this->subscriber == NULL) {
			$this->responseError(1,1, "Wifi_number is invalid");
		}				
		if(!$this->subscriber->isUsingService()) {
			$this->responseError(1,1, "Thuê bao ".$this->msisdn." chưa đăng ký gói dịch vụ nào của vFilm");
		}
		
		$result = $this->subscriber->generateWifiPassword();
		if($result < 0) {
			$this->responseError(1,1, "Xảy ra lỗi trong quá trình gửi mật khẩu cho thuê bao ".$this->msisdn." Xin quý khách vui lòng thử lại sau");
		}
		else {
			$this->responseError(0,0, "Đã gửi mật khẩu cho thuê bao ".$this->msisdn. ". Mật khẩu chỉ có thể dùng được một lần duy nhất.");
		}
// 		$expiryTime = date("d/m/Y H:i:s", time() + 86400);
// 		if($result < 0) {
// 			$this->responseError(1,1, "Xảy ra lỗi trong quá trình gửi mật khẩu cho thuê bao ".$this->msisdn." Xin quý khách vui lòng thử lại sau");
// 		}
// 		else {
// 			$this->responseError(0,0, "Đã gửi mật khẩu cho thuê bao ".$this->msisdn. ". Mật khẩu có thời hạn đến ".$expiryTime);
// 		}
	}
	
	public function actionLogin() {
		if($this->accessType == Controller::$ACCESS_VIA_WIFI) {
			if(($this->wifi_number != -1) && ($this->subscriber == NULL)) {
				$this->msisdn = CUtils::validatorMobile($this->wifi_number);
				$this->subscriber = Subscriber::newSubscriber($this->msisdn);
			}
		}
		$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : -1;
		if($this->subscriber == NULL) {
			$this->responseError(1,1, "Số điện thoại để đăng nhập wifi không đúng");
		}
		
		
// 		$response = $this->subscriber->loginByWifiPassword($password);
// 		if($response == 0) {
// 			$this->responseError(0, 0, "Quý khách đã đăng nhập thành công!");
// 		}
// 		else {
// 			$this->responseError(0, 0, "Quý khách đăng nhập không thành công. Mật khẩu nhập vào sai hoặc đã sử dụng rồi.");
// 		}
		Yii::log("Login wifi msisdn $this->msisdn pw $password");
		$result = $this->subscriber->loginByWifiPassword($password);
		Yii::log("Login wifi result: "+$result['status']);
		$this->_sessionID = $result['session_id'];
		$this->responseError($result['status'], $result['status'], $this->subscriber->getErrorMessage($result['status']));
	}

	public function actionLogout() {
		if($this->subscriber == NULL) {
			$this->responseError(1,1, "Số điện thoại để đăng nhập wifi không đúng");
		}
		$arrSession = SubscriberSession::model()->findAllByAttributes(array("subscriber_id" => $this->subscriber->id, "status" => 1));
		foreach($arrSession as $session) {
			$session->delete();
		}
		$this->responseError(0,0, "Đăng xuất thành công!");
	}
	
	public function actionSendSubscriberFeedback() {
		$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : -1;
		$content = isset($_REQUEST['content']) ? $_REQUEST['content'] : -1;
		if($this->subscriber == NULL) {
			$this->responseError(1,1, "Vui lòng sử dụng 3G của VinaPhone để thực hiện giao dịch này.");
		}
		
		$feedback = new SubscriberFeedback();
		$feedback->subscriber_id = $this->subscriber->id;
		$feedback->title = $title; 
		$feedback->content = $content;
		$feedback->create_date = new CDbExpression("NOW()");
		$feedback->status = 1;
		$feedback->is_responsed = 0;
		if(!$feedback->save()) {
			$this->responseError(1,1, "Xảy ra lỗi khi nhận phản hồi. Xin vui lòng thử lại sau.");
		} 
		$this->responseError(0,0, "Nội dung phản hồi đã được lưu lại. Cám ơn bạn đã góp ý!");
	}
	
	public function actionAdminRegisterService() {
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		if(!ClientAuthen::checkLocalAddress($remoteAddr)) {
			$this->responseError(1,1, "This api must be called from localhost. Not from $remoteAddr");
		}
	
		$service = Service::model()->findByAttributes(array('is_active'=>1));
		if($service == NULL) {
			$this->responseError(1,1, "Service is not existed");
		}
	
		$user_name = isset($_REQUEST['user_name']) ? $_REQUEST['user_name'] : NULL;
		$user_ip = isset($_REQUEST['user_ip']) ? $_REQUEST['user_ip'] : NULL;
		$subscriber_number = isset($_REQUEST['phone_number']) ? $_REQUEST['phone_number'] : -1;
		$subscriber_number = CUtils::validatorMobile($subscriber_number);
		$subscriber = Subscriber::model()->findByAttributes(array('subscriber_number' => $subscriber_number));
		if($subscriber == NULL){
			$subscriber = Subscriber::newSubscriber($subscriber_number);
			Yii::log("subscriber NULL\n");
		}
		Yii::log("subscriber_number = ".$subscriber->subscriber_number."\n");
		Yii::log("ChargingProxy_test::processSubService \n");
		$registerStatus = ChargingProxy_test::processSubService(CHANNEL_TYPE_CSKH, ChargingProxy_test::ACTION_REGISTER, $service, $subscriber, NULL, $user_name, $user_ip);
		Yii::log("ChargingProxy_test::processSubService status response = $registerStatus \n");
		if($registerStatus == ChargingProxy_test::PROCESS_ERROR_NONE) {
			$this->responseError(0, 0, "Đăng ký dịch vụ thành công");
		}
		else {
			$this->responseError($registerStatus, $registerStatus, ChargingProxy_test::getNameErrorCode($registerStatus));
		}
	}



    public function actionAdminRegisterService2() {
        $remoteAddr = $_SERVER['REMOTE_ADDR'];
        if(!ClientAuthen::checkLocalAddress($remoteAddr)) {
            $this->responseError(1,1, "This api must be called from localhost. Not from $remoteAddr");
        }

        $service = Service::model()->findByAttributes(array('is_active'=>1));
        if($service == NULL) {
            $this->responseError(1,1, "Service is not existed");
        }

        $user_name = isset($_REQUEST['user_name']) ? $_REQUEST['user_name'] : NULL;
        $user_ip = isset($_REQUEST['user_ip']) ? $_REQUEST['user_ip'] : NULL;
        $subscriber_number = isset($_REQUEST['phone_number']) ? $_REQUEST['phone_number'] : -1;
        $subscriber_number = CUtils::validatorMobile($subscriber_number);
        $subscriber = Subscriber::model()->findByAttributes(array('subscriber_number' => $subscriber_number));
        if($subscriber == NULL){
            $subscriber = Subscriber::newSubscriber($subscriber_number);
        }
        Yii::log("subscriber_number = ".$subscriber->subscriber_number."\n");
        Yii::log("ChargingProxy_test::processSubService \n");
        $registerStatus = ChargingProxy_test::processSubService(CHANNEL_TYPE_ADMIN, ChargingProxy_test::ACTION_REGISTER, $service, $subscriber, NULL, $user_name, $user_ip);
        Yii::log("ChargingProxy_test::processSubService status response = $registerStatus \n");
        if($registerStatus == ChargingProxy_test::PROCESS_ERROR_NONE) {
            $this->responseError(0, 0, "Đăng ký dịch vụ thành công");
        }
        else {
            $this->responseError($registerStatus, $registerStatus, ChargingProxy_test::getNameErrorCode($registerStatus));
        }
    }
	
	public function actionAdminCancelService() {
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		if(!ClientAuthen::checkLocalAddress($remoteAddr)) {
			$this->responseError(1,1, "This api must be called from localhost. Not from $remoteAddr");
		}
	
		$service = Service::model()->findByAttributes(array('is_active'=>1));
		if($service == NULL) {
			$this->responseError(1,1, "Service is not existed");
		}
	
		$user_name = isset($_REQUEST['user_name']) ? $_REQUEST['user_name'] : NULL;
		$user_ip = isset($_REQUEST['user_ip']) ? $_REQUEST['user_ip'] : NULL;
		$subscriber_number = isset($_REQUEST['phone_number']) ? $_REQUEST['phone_number'] : -1;
		$subscriber_number = CUtils::validatorMobile($subscriber_number);
		$subscriber = Subscriber::newSubscriber($subscriber_number);
		if($subscriber == NULL) {
			$this->responseError(1,1, "Không nhận diện được thuê bao: $subscriber_number");
		}
	
		$ssm = ServiceSubscriberMapping::model()->findByAttributes(array("subscriber_id" => $subscriber->id, "is_active" => 1));
		/* @var $ssm ServiceSubscriberMapping */
		if($ssm == NULL) {
			$this->responseError(1,1, "Thuê bao $subscriber_number chưa đăng ký dịch vụ nào");
		}
	
		$cancelStatus = ChargingProxy_test::processSubService(CHANNEL_TYPE_CSKH, ChargingProxy_test::ACTION_CANCEL, $ssm->service, $subscriber, NULL, $user_name, $user_ip);
		if($cancelStatus == ChargingProxy_test::PROCESS_ERROR_NONE) {
			$this->responseError(0, 0, "Hủy dịch vụ thành công");
		}
		else {
			$this->responseError($cancelStatus, $cancelStatus, ChargingProxy_test::getNameErrorCode($cancelStatus));
		}
	}
	
	public function actionAdminRegisterRecurring() {
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		if(!ClientAuthen::checkLocalAddress($remoteAddr)) {
			$this->responseError(1,1, "This api must be called from localhost. Not from $remoteAddr");
		}
	
		$subscriber_number = isset($_REQUEST['phone_number']) ? $_REQUEST['phone_number'] : -1;
		$subscriber_number = CUtils::validatorMobile($subscriber_number);
		$subscriber = Subscriber::newSubscriber($subscriber_number);
		if($subscriber == NULL) {
			$this->responseError(1,1, "Không nhận diện được thuê bao: $subscriber_number");
		}
	
		$subscriber->auto_recurring = 1;
		if($subscriber->update()) {
			$this->responseError(0, 0, "Đăng ký tự động gia hạn thành công");
		}
		else {
			$this->responseError(1, 1, "Đăng ký tự động gia hạn thất bại");
		}
	}
	
	public function actionAdminCancelRecurring() {
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		if(!ClientAuthen::checkLocalAddress($remoteAddr)) {
			$this->responseError(1,1, "This api must be called from localhost. Not from $remoteAddr");
		}
	
		$subscriber_number = isset($_REQUEST['phone_number']) ? $_REQUEST['phone_number'] : -1;
		$subscriber_number = CUtils::validatorMobile($subscriber_number);
		$subscriber = Subscriber::newSubscriber($subscriber_number);
		if($subscriber == NULL) {
			$this->responseError(1,1, "Không nhận diện được thuê bao: $subscriber_number");
		}
	
		$subscriber->auto_recurring = 0;
		if($subscriber->update()) {
			$this->responseError(0, 0, "Hủy tự động gia hạn thành công");
		}
		else {
			$this->responseError(1, 1, "Hủy tự động gia hạn thất bại");
		}
	}

	//for reset number test
    public function actionResetTestNumber(){
        $msisdn = isset($_REQUEST['msisdn']) ? $_REQUEST['msisdn'] : "841237948137";
        $this->subscriber = Subscriber::model()->findByAttributes((array('subscriber_number'=>$msisdn)));
        $channel_type = CHANNEL_TYPE_CSKH;
        echo ChargingProxy::syncSubscriber($channel_type, NULL, $service, $subscriber);
        echo $this->deleteServiceMapping();
    }

    public function actionSubscriberCancel(){
        if (!isset($_POST['subscriber_number'])){
            echo 'error';
            die;
        }
        $subscriber_number = $_POST['subscriber_number'];
        if (Subscriber::model()->exists("subscriber_number = $subscriber_number")){
            $subscriber = Subscriber::model()->findByAttributes((array('subscriber_number'=>$subscriber_number)));
        }
        else{
            $subscriber = new Subscriber();
            $subscriber->subscriber_number = $subscriber_number;
            $subscriber->user_name = $subscriber_number;
            $subscriber->create_date = date('Y-m-d H:i:s');
            $subscriber->modify_date = date('Y-m-d H:i:s');
            $subscriber->save();
        }
        $channel_type = CHANNEL_TYPE_CSKH;
        echo ChargingProxy::syncSubscriber2($channel_type, NULL, 5, $subscriber);
    }

	public function cancelService() {
		$message = "";
		$service = Service::model()->findByAttributes(array('is_active' => 1));
		if($service == NULL) {
			$message .= "Không tồn tại dịch vụ";
		}

		if($this->subscriber == NULL) {
			$message .= " Không nhận diện được thuê bao";
			return;
		}

		$cancelStatus = ChargingProxy::processSubService(CHANNEL_TYPE_SYSTEM, ChargingProxy::ACTION_CANCEL, $service, $this->subscriber);
		if($cancelStatus == ChargingProxy::PROCESS_ERROR_NONE) {
			$message .=  " Hủy dịch vụ thành công";
		}
		else {
			$message .=  " Hủy dịch vụ không thành công";
		}
		return $message;
	}
	public function deleteServiceMapping(){
		$sql = "delete from service_subscriber_mapping where subscriber_id = ".$this->subscriber->id;
		Yii::app()->db->createCommand($sql)->query();
		return "Reset number success";
	}
	public function actionGetFlag($flag = 1) {
		$this->responseFlagCharging($flag);
	}

    public function actionCancelLocalService(){
        if (!isset($_POST['msisdn'])){
            echo 'error';
            die;
        }
        $msisdn = $_POST['msisdn'];
        $subscriber = Subscriber::model()->findByAttributes(array('subscriber_number' => $msisdn));
        if (!$subscriber){
            $subscriber = Subscriber::newSubscriber($msisdn);
        }
        $usingService = ServiceSubscriberMapping::model()->findByAttributes(array('subscriber_id' => $subscriber->id, 'is_active' => 1, 'is_deleted' => 0));
        if (!$usingService){
            $message = "Thue bao $msisdn khong su dung dich vu\n";
            echo $message;
            return $message;
        }
        $service = Service::model()->findByAttributes(array('id' => $usingService->service_id));
        $transaction = $subscriber->newTransaction('WAP', null, 4, $service);
        $transaction->description =  'HUY_*';
        $transaction->status =  1;
        $transaction->save();
        $usingService->is_active = 0;
        $usingService->is_deleted = 1;
        $usingService->save();
        $message = "Da huy dich vu cua $msisdn\n";
        echo $message;
        return $message;
    }
}

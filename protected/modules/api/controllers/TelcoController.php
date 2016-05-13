<?php

// lam theo mo ta cua tai lieu "3.1.Yêu cầu API tích hợp với VNP.docx"
class TelcoController extends CController
{
    protected $_sessionID = "";
    protected $_device_type_id = ""; // identify device type: android 1.6, 2.3, 4.0, iPhone3GS, iPhone4...
    protected $_app_version_code = "";
    protected $_username = "";
    protected $_password = "";
    protected $_format = 'xml';
    protected $_isTablet = 0; // 0 - not tablet, 1 - is tablet
    protected $wifi_number = "";
    public $subscriber;
    public $services;
    public $usingServices;
    public $isHome = false;
    public $inAccount = false;
    public $accessType;
    public $user_session;
    protected $crypt;

    const  ACTION_REGISTER = 1;
    const  ACTION_CANCEL = 3;
// 	MOBILE_ADS, VASPORTAL, VASDEALER, CCOS
    const APPLICATION_VASGW = 'VASGW';
    const APPLICATION_MOBILE_ADS = 'MOBILE_ADS';
    const APPLICATION_VASPORTAL = 'VASPORTAL';
    const APPLICATION_VASDEALER = 'VASDEALER';
    const APPLICATION_CCOS = 'CCOS';
    const  ACTION_REGISTER_TEST = 'SOTEST';

    protected $pageSize = 10;
    protected $haveCookie = true;

    public function beforeAction($action)
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR'];
        if (!ClientAuthen::checkLocalAddress($remoteAddr)) {
            $this->responseError(111, "This api must be called from localhost. Not from $remoteAddr");
        }
        return parent::beforeAction($action);
    }

// 	3.6 Lấy thông tin giao dịch của thuê bao
// 	http://hocde.vn/api/telco/getTransactions?requestid=1&msisdn=84916731158&note=vnp&application=vnp_api&channel=api&username=chau&userip=127.0.0.1&fromdate=20131110&todate=20131120
    public function actionGetTransactions($requestid = NULL, $msisdn = NULL, $fromdate = NULL, $todate = NULL, $pagesize = 10, $pageindex = 1, $application = 'TEST', $channel = 'API', $username = null, $userip = null)
    {
        $pageindex--; //page index cua code tinh tu 0
        $getTransResult = 110;
        try {
            $msisdn = CUtils::validatorMobile($msisdn);
            $subscriber = Subscriber::newSubscriber($msisdn);
            /* @var $subscriber Subscriber */
            if ($subscriber == NULL) {
                $getTransResult = 101;
                throw new Exception("Thue bao ko ton tai");
            }
            $fDate = ($todate == NULL) ? date('Y-m-d H:i:s', time() - 7 * 86400) : date('Y-m-d H:i:s', strtotime($fromdate));
            $tDate = ($todate == NULL) ? date('Y-m-d H:i:s', time()) : date('Y-m-d H:i:s', strtotime($todate));

            $res = $subscriber->getSubscriberTransactionsHistory($fDate, $tDate, $pageindex, $pagesize);

            header("Content-type: text/xml; charset=utf-8");
            $xmlDoc = new DOMDocument();
            $xmlDoc->encoding = "UTF-8";
            $xmlDoc->version = "1.0";

            //TODO: authen, session, error handle
            $root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
// 			$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
            $root->appendChild($xmlDoc->createElement("errorid", "0"));
            $root->appendChild($xmlDoc->createElement("errordesc", "Lấy danh sách giao dịch thành công"));
            $result = $root->appendChild($xmlDoc->createElement("result"));

            $result->appendChild($xmlDoc->createElement('pageindex', CHtml::encode($res['page_number'] + 1)));
            $result->appendChild($xmlDoc->createElement('pagesize', CHtml::encode($res['page_size'])));
            $result->appendChild($xmlDoc->createElement('total_page', CHtml::encode($res['total_page'])));
            $result->appendChild($xmlDoc->createElement('total_result', CHtml::encode($res['total_result'])));
//            echo '<pre>'; print_r($res['data']);die;
            foreach ($res['data'] as $transaction) {
                $transactionNode = $xmlDoc->createElement('transaction');
                $sType = SubscriberTransaction::getTypeName($transaction['purchase_type']);
                if (isset($transaction['req_id'])) { //giao dich nay do VNP goi
                    $sDesc = $sType . " qua kênh " . $transaction['channel_type'];
                } else {
                    $sDesc = $sType . " " . $transaction['description'] . " qua kênh " . $transaction['channel_type'];
                }
                $transactionNode->appendChild($xmlDoc->createElement("description", $sDesc));
                $transactionNode->appendChild($xmlDoc->createElement("create_date", date('YmdHis', strtotime($transaction['create_date']))));
                $sCost = intval($transaction['cost']) . "đ";
                $transactionNode->appendChild($xmlDoc->createElement("cost", $sCost));
                $result->appendChild($transactionNode);
            }
            $xmlDoc->formatOutput = true;
            $content = $xmlDoc->saveXML();
            echo $content;
        } catch (Exception $e) {
            $this->responseError($getTransResult, $this->getTransErrorMsg($getTransResult));
        }
    }

    public function actionGetAccountInfo()
    {
        if ($this->subscriber == NULL) {
            $this->responseError(1, "Không nhận diện được thuê bao");
        }

        header("Content-type: text/xml; charset=utf-8");
        $xmlDoc = new DOMDocument();
        $xmlDoc->encoding = "UTF-8";
        $xmlDoc->version = "1.0";

        //TODO: authen, session, error handle
        $root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
        $root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
        $root->appendChild($xmlDoc->createElement("session", $this->_sessionID ? $this->_sessionID : ""));
        $root->appendChild($xmlDoc->createElement("action", $this->action->id));
        $root->appendChild($xmlDoc->createElement("error_no", "0"));
        $result = $root->appendChild($xmlDoc->createElement("result"));

        $usingServiceCodeName = "";
        $usingServiceExpiryDate = "";
        if (count($this->usingServices) > 0) {
            $usingServiceCodeName = $this->usingServices[0]->service->code_name;
            $usingServiceExpiryDate = $this->usingServices[0]->expiry_date;
        }
        $result->appendChild($xmlDoc->createElement("current_service", $usingServiceCodeName));
        $result->appendChild($xmlDoc->createElement("expiry_date", $usingServiceExpiryDate));
        $emailNode = $result->appendChild($xmlDoc->createElement("email", $this->subscriber->email));
        $emailStatus = (isset($this->subscriber->verification_code) && $this->subscriber->verification_code != 0) ? 0 : 1;
        $emailNode->appendChild($xmlDoc->createAttribute("email_status"))
            ->appendChild($xmlDoc->createTextNode($emailStatus));
        $wifiUsing = ($this->accessType == self::$ACCESS_VIA_WIFI) ? 1 : 0;
        $result->appendChild($xmlDoc->createElement("wifi_using", $wifiUsing));

        $xmlDoc->formatOutput = true;
        $content = $xmlDoc->saveXML();
        echo $content;
    }

    public function actionGetServices()
    {
        header("Content-type: text/xml; charset=utf-8");
        $xmlDoc = new DOMDocument();
        $xmlDoc->encoding = "UTF-8";
        $xmlDoc->version = "1.0";

        //TODO: authen, session, error handle
        $root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
        $root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
        $root->appendChild($xmlDoc->createElement("session", $this->_sessionID ? $this->_sessionID : ""));
        $root->appendChild($xmlDoc->createElement("action", $this->action->id));
        $root->appendChild($xmlDoc->createElement("error_no", "0"));
        $result = $root->appendChild($xmlDoc->createElement("result"));

        $arrServicesNode = $result->appendChild($xmlDoc->createElement("services"));
        $arrServices = Service::model()->findAllByAttributes(array("is_active" => 1));
        foreach ($arrServices as $service) {
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

    public function actionSendService()
    {
        if ($this->subscriber == NULL || $this->accessType != Controller::$ACCESS_VIA_WIFI) {
            $this->responseError(1, "Vui lòng sử dụng 3G của Viettel để thực hiện giao dịch này.");
        }

        $serviceType = isset($_REQUEST['service_type']) ? $_REQUEST['service_type'] : "-1";
        $receiverNumber = isset($_REQUEST['receiver']) ? $_REQUEST['receiver'] : "-1";
        $receiverNumber = CUtils::validatorMobile($receiverNumber);
        if ($receiverNumber == '') {
            $this->responseError(1, "Số điện thoại người nhận không hợp lệ");
        }

        $receiver = Subscriber::newSubscriber($receiverNumber);
        $service = Service::model()->findByAttributes(array('is_active' => 1));

        $giftConfirm = SubscriberGiftConfirmation::newGiftConfirm($this->subscriber, $receiver, $service);
        if ($giftConfirm == NULL) {
            $this->responseError(1, "Xảy ra lỗi trong quá trình tặng gói $serviceType cho thuê bao $receiverNumber. Xin quý khách vui lòng thử lại sau");
        }

        $content = "Quy khach duoc tang goi " . $service->code_name . " xem mien phi dich vu phim truc tuyen iFilm cua MobiFone tu so may " . $this->subscriber->subscriber_number . ". Ban hay nhan tin \"CO " . $giftConfirm->confirmation_code . " \"gui 9033 de nhan qua. Truy cap http://ifilm.vn de su dung dich vu";
        $mt = VinaphoneController::sendSms($receiver->subscriber_number, $content);
        if ($mt->mt_status == 0) {
            $this->responseError(1, "Xảy ra lỗi trong quá trình tang goi dich vu $serviceType cho thuê bao $receiverNumber. Xin quý khách vui lòng thử lại sau");
        }

        $strContent = "Đã gửi tặng dịch vụ. Bạn vui lòng đợi xác nhận của thuê bao " . $receiver->subscriber_number;
        $this->responseError(0, 0, $strContent);
    }

    /*
        1
        requestid
        Mã ngẫu nhiên
        2
        msisdn
        Số thuê bao
        3
        packagename
        Mã gói dịch vụ
        4
        promotion
        Số chu kỳ, ngày, tuần hay tháng miễn phí. Sẽ tự động gia hạn sau khi hết khuyến mãi.
        0: đăng ký như bình thường.
        Nc: miễn cước N chu kỳ.
        Nd: miễn cước dùng N ngày.
        Nw: miễn cước dùng N tuần.
        Nm: miễn cước dùng N tháng.

        Hỗ trợ ít nhất các giá trị sau:
        - 7d: 7 ngày
        - 1m: 1 tháng tính theo công thức add_month
        - 1c: tùy theo chu kỳ cước của dịch vụ đó
        5
        trial
        Số chu kỳ, ngày, tuần hay tháng dùng thử. Sẽ gửi tin nhắn thông báo khi hết thời gian dùng thử, nếu khách hàng không hủy thì sẽ bị gia hạn.
        0: đăng ký như bình thường.
        Nc: được dùng thử N chu kỳ.
        Nd: được dùng thử N ngày.
        Nw: được dùng thử N tuần.
        Nm: được dùng thử N tháng.
        Hỗ trợ ít nhất các giá trị sau:
        - 1m: 1 tháng tính theo công thức add_month
        6
        bundle *** khong dung nua
        0: đăng ký gói bình thường
        1: đăng ký gói kiểu bundle (không trừ cước đăng ký, không gia hạn)
        7
        note
        Chú thích về đăng ký/khuyến mãi/dùng thử hoặc là tên gói bundle.
        Trường hợp đăng ký mà phải gửi lệnh vào cổng charging thì lấy trường note này để truyền vào lệnh gửi vào cổng charging.
        8
        application ** VAS Submanager, VAS Voucher, VAS Portal, CCOS, VAS Dealer, VinaNet
        Tên hệ thống gọi API (sẽ có xử lý logic tùy giá trị)
        Logic xử lý đối với trường application sẽ phụ thuộc và kịch bản kinh doanh quy định. Ví dụ application là CCOS, VASPORTAL, VASDEALER, …
        9
        channel
        Kênh xuất phát lệnh (SMS, WEB, WAP, USSD…)
        10
        username
        Tên của người dùng thao tác (thao tác viên)
        11
        userip
        IP của người dùng thao tác (thao tác viên, để log không dùng để xác thực)
        ?requestid=1&msisdn=84916731158&packagename=VFILM&promotion=1w&trial=0&bundle=1&note=vnp&application=vnp_api&channel=api&username=chau&userip=127.0.0.1
        3.1
            http://hocde.vn/api/telco/registerService?requestid=1&msisdn=84916731158&packagename=VFILM&promotion=1w&trial=1w&bundle=1&note=vnp&application=vnp_api&channel=api&username=chau&userip=127.0.0.1 */
    public function actionRegisterService($requestid = null, $msisdn = null, $packagename = VFILM, $promotion = null, $trial = null, $bundle = null, $note = null, $application = 'TEST', $channel = 'API', $username = null, $userip = null)
    {
// 		Yii::log("*** 1 *** ".microtime());
        Yii::log("TestVtv ***:  requestid:" . $requestid . "|msisdn: " .$msisdn ."|packagename: ". $packagename ."|promotion". $promotion ."|trial: ". $trial ."|bundle: ". $bundle ."|note". $note ."|application: ". $application ."|channel: ". $channel ."|username: ". $username ."|userip: ". $userip ." *** ");
        $registerResult = 110;
        if ($application == self::APPLICATION_MOBILE_ADS) {
            $channel = 'API';
        }
        Yii::log("*** note_ *** ".$note);
        $partnerId = Partner::model()->findByPk($note);
        $partnerIds = null;
        if($partnerId != null){
            $channel = 'WAP';
            $partnerIds = $partnerId->id;
        }
        if(strpos ($note, 'AMOBI') !== false){
            $partnerIds = 9;
        }
        if(strpos ($note, 'CLEVERNET') !== false){
            $partnerIds = 11;
        }

        try {
            $msisdn = CUtils::validatorMobile($msisdn);
            if (!isset($msisdn)) {
                $registerResult = 101;
                throw new Exception("Khong phai thue bao cua VinaPhone");
            }
            $subscriber = Subscriber::newSubscriber($msisdn);

            /* @var $subscriber Subscriber */
            if ($subscriber == NULL) {
                throw new Exception("Loi he thong - tao thue bao $msisdn loi");
            }
            if (isset($note)) {
                $aDate = new DateTime();
                $mo = explode('|', $note);
                Yii::log("TelcoController *** " . $mo . " *** ");
                if (trim($mo[0]) == 'MO') {
                    $mt = new SmsMessage();
                    $mt->type = $mo[0];
                    $mt->source = $mo[3];
                    $mt->destination = $mo[2];
                    $mt->message = $mo[1];
                    $mt->received_time = $aDate->format('Y-m-d H:i:s');
                    $mt->sending_time = $aDate->format('Y-m-d H:i:s');
                    if (isset($response) && isset($response->body)) {
                        $mt->mt_status = $response->body;
                    }
                    if (isset($subscriber))
                        $mt->subscriber_id = $subscriber->id;
                    $mt->save();
                }
            }
// 			Yii::log("*** 2 *** ".microtime());
            /* @var $ssm ServiceSubscriberMapping */
            $ssm = NULL;
            if ($subscriber->isUsingService()) {
                if ($subscriber->auto_recurring == 0) {
// 					thue bao dang ky roi nhu ko tu dong gia han. Sua lai thanh tu dong gia han
                    $subscriber->auto_recurring = 1;
                    $subscriber->update();
                    $registerResult = 2;
                    $ssm = $subscriber->getUsingService();
                } else {
                    $registerResult = 1;
                    throw new Exception("Thue bao da dang ky roi");
                }
            }
// 			Yii::log("*** 3 *** ".microtime());
            $service = Service::model()->findByAttributes(array('is_active' => 1));

            //uu tien trial truoc, promotion sau
            $cost = $service->price;

// 			if($bundle == 1) { *** VNP ko yeu cau param nay nua
// 				$cost = 0;
// 				$subscriber->auto_recurring = 0;
// 				$subscriber->update();
// 			}

            //TODO: xu ly the nao voi $application. Tam thoi luu vao description cua ssm
// 			Yii::log("*** 4 *** ".microtime());
            $trial_days = 0;
            $using_days = 0;
            $continueCheck = true;
            $isPromotionTrans = false;
            if (isset($trial)) {
                Yii::log("co");
                $trial_days = $this->getUsingDaysByPromotion($trial, $service);
                if ($trial_days > 0) {
                    $isPromotionTrans = true;
                    $continueCheck = false;
                    $cost = 0;
                }
            }
            if ($continueCheck) {
                Yii::log("khong");
                if (isset($promotion)) {
                    $using_days = $this->getUsingDaysByPromotion($promotion, $service);
                    if ($using_days > 0) {
                        $isPromotionTrans = true;
                        $cost = 0;
                    }
                }
            }
            $isRegisteredInThreeMonth = ServiceSubscriberMapping::model()->isRegisteredInThreeMonth($subscriber->id);
            if ($trial_days == 0 && $using_days == 0) {
                //$number_day = $service->using_days;
                $number_day = 1;
            } else {
                $number_day = $trial_days > 0 ? $trial_days : $using_days;
            }

            if ($subscriber->status == Subscriber::STATUS_WHITE_LIST || !$isRegisteredInThreeMonth) {
                $cost = 0;
            }
            $isFirstUse = false;
            if ($cost > 0) {
                if ($subscriber->isFirstUse()) {
                    $cost = 0;
                }
            }

            if ($channel == CHANNEL_TYPE_ADMIN){
                $cost = 0;
            }

            if ($cost == 0) {
                $isFirstUse = true;
                $isPromotionTrans = true;
            }

// 			Yii::log("*** 5 *** ".microtime());
            Yii::log("newTransaction actionRegisterService subscriber_number: " . $msisdn . "channel_type" . $channel);
            if ($channel != CHANNEL_TYPE_ADMIN){
                $trans = $subscriber->newTransaction($channel, USING_TYPE_REGISTER, PURCHASE_TYPE_NEW, $service, null, null);
            }else{
                $trans = new SubscriberTransaction();
            }

            $trans->req_id = $requestid;
            if ($trans->cost != $cost) {
                $trans->cost = $cost;
            }
// 			Yii::log("*** 6 *** ".microtime());
            $freeFor1stTimeByVnp = false;
            $freeFor1day = false;


            $chargingResult = CPS_OK;
            if ($isPromotionTrans) {
//				if($subscriber->id == 491006 || $subscriber->id == 240){
//                    $freeFor1day = true;
//					$c_response = ChargingProxy_test::chargingRegister2($username, $userip, $msisdn, $service, $trans->id, $promotion, 0, $trial, $bundle, $channel, $note);
//					$chargingResult = $c_response->error;
//				} else {
                if ($channel != CHANNEL_TYPE_ADMIN){
                    $chargingResult = ChargingProxy::chargingPromotionRegister($msisdn, $service, $trans->id, 0, $channel, $note);
                }else{
                    $chargingResult = ChargingProxy::chargingPromotionRegister($msisdn, $service, time(), 0, $channel, $note);
                }
//				}
            } else {
//				$chargingResult = ChargingProxy::chargingRegister($msisdn, $service, $trans->id, false, intval($trans->cost), $channel, $note);
//				if($subscriber->id == 491006 || $subscriber->id == 240){
//					$c_response = ChargingProxy_test::chargingRegister2($username, $userip, $msisdn, $service, $trans->id, $promotion, intval($trans->cost), $trial, $bundle, $channel, $note);
//					$chargingResult = $c_response->error;
//				} else {
                $c_response = ChargingProxy::chargingRegister2($msisdn, $service, $trans->id, false, intval($trans->cost), $channel, $note);
                $chargingResult = $c_response->return;
//				}
                if (isset($c_response->promotion) && isset($c_response->note) && isset($c_response->price) &&
                    $c_response->promotion == '1' && $c_response->price == '0' && $c_response->note == 'BIG032014'
                ) {
                    $freeFor1stTimeByVnp = true;
                }
            }
// 			Yii::log("*** 7 *** ".$chargingResult);
            $trans->error_code = $chargingResult;
            $isChargingSuccess = false;

//			if($subscriber->id == 491006 || $subscriber->id == 240){
//				if($chargingResult == CPS_OK || $chargingResult == CPS_OK_3 || $chargingResult == CPS_OK_4 || $chargingResult == CPS_OK_6 || $chargingResult == CPS_OK_7 || $chargingResult == CPS_OK_8) {
//					if($registerResult != 2) { //khong phai truong hop: dky roi, dang ko tu dong gia han, dky lai va cho tu dong gia han
//						if($cost > 0) {
//							$registerResult = 4; //dky thanh cong va bi tru cuoc
//						}
//						else if($cost == 0) {
//							$registerResult = 3; //dky thanh cong va khong bi tru cuoc
//						}
//						else {
//							$registerResult = 0;
//						}
//					}
//					$trans->status = 1;
//
//					if ($freeFor1stTimeByVnp || $isRegisteredInThreeMonth == false){
//
//						$mtMessage = $this->getMTContent(self::ACTION_REGISTER, $application, true, $number_day);
//						$registerResult = 3; //dky thanh cong va khong bi tru cuoc
//						$trans->cost = 0;
//					} else {
//						$mtMessage = $this->getMTContent(self::ACTION_REGISTER, $application, $isFirstUse, $number_day);
//					}
//
//					if($application != 'IOS') {
//						$res = VinaphoneController::sendSms($msisdn, $mtMessage);
//					}
//			}
//			}else

            if ($chargingResult == CPS_OK || $chargingResult == CPS_OK_11) {
                if ($registerResult != 2) { //khong phai truong hop: dky roi, dang ko tu dong gia han, dky lai va cho tu dong gia han
                    if ($cost > 0) {
                        $registerResult = 4; //dky thanh cong va bi tru cuoc
                    } else if ($cost == 0) {
                        $registerResult = 3; //dky thanh cong va khong bi tru cuoc
                    } else {
                        $registerResult = 0;
                    }
                }
                $trans->status = 1;
                //dang ky qua channel CSKH-ADMIN thi khong delay sms -> ko gui sms
                if ($application != 'IOS' && $channel != CHANNEL_TYPE_ADMIN) {
                    if ($freeFor1stTimeByVnp || $isRegisteredInThreeMonth == false) {
                        $mtMessage = $this->getMTContent(self::ACTION_REGISTER, $application, true, $number_day, $subscriber->id);
                        $registerResult = 3; //dky thanh cong va khong bi tru cuoc
                        $trans->cost = 0;
                    } else {
                        $mtMessage = $this->getMTContent(self::ACTION_REGISTER, $application, $isFirstUse, $number_day, $subscriber->id);
                    }
                }else{
                    if ($freeFor1stTimeByVnp || $isRegisteredInThreeMonth == false) {
                        $registerResult = 3; //dky thanh cong va khong bi tru cuoc
                        $trans->cost = 0;
                    }
                }

                if ($application != 'IOS' && $channel != CHANNEL_TYPE_ADMIN) {
                    if (!empty($mtMessage)) {
                        $res = VinaphoneController::sendSms($msisdn, $mtMessage);
                    }
                }
            } else if ($chargingResult == NOK_NO_MORE_CREDIT_AVAILABLE) {//thuong ko giao dich thanh cong do loi nay: loi thieu tien
                $registerResult = 5;
                $trans->status = 2;
            } else if ($chargingResult == CHARGING_ERROR_SUB_NOT_EXIST) {//Thue bao khong ton tai
                $registerResult = 102;
                $trans->status = 2;
            } else { //giao dich ko thanh cong do loi khac (ko phai thieu tien)
                $registerResult = 100;
                $trans->status = 2;
            }

            Yii::log("TelcoContrller Channel: $channel, msisdn: $msisdn, chargingResult: $chargingResult");

            Yii::log("*** TelcoController *** " . "_app_$application" . "_trial_$trial" . "_promotion_$promotion" . "_note_$note");
            $trans->description = "_app_$application" . "_trial_$trial" . "_promotion_$promotion" . "_note_$note";
            $trans->vnp_username = $username;
            $trans->vnp_ip = $userip;
            if ($channel != CHANNEL_TYPE_ADMIN){
                $trans->update();
                if ($trans->status != 1) {
                    throw new Exception("Giao dich ko thanh cong");
                }
            }

// 			Yii::log("*** 9 *** ".microtime());

            //den day nghia la giao dich da thanh cong, xu ly ssm
            if ($ssm == NULL) {
                if ($channel != CHANNEL_TYPE_ADMIN){
                    $ssm = $subscriber->addService($service, $partnerIds, USING_TYPE_REGISTER, null);
                }

            }
//            if ($subscriber->id == 240) {
//                Yii::log("requestid2 *** 1" . $requestid . " *** ");
                $subActivityLog = SubscriberActivityLog::model()->findByPk($requestid);

                if ($subActivityLog != null) {
//                    Yii::log("requestid2 *** 2" . $requestid . " *** ");
                    $subActivityLog->status = 1;
                    $subActivityLog->save();
                }
//            }
// 			Yii::log("*** 10 *** ".microtime());
            $continueCheck = true; //neu VNP truyen tham so trial (!= 0) roi thi khong tiep tuc check tham so promotion nua
//            if($subscriber->id == 491006 || $subscriber->id == 240){
            if ($freeFor1day && $channel != CHANNEL_TYPE_ADMIN) {
                $continueCheck = false;
                $ssm->expiry_date = date('Y-m-d 23:59:59');
                $ssm->update();
            } else {
                if (isset($trial)) {
                    if ($trial_days > 0 && $channel != CHANNEL_TYPE_ADMIN) {
                        $continueCheck = false;
                        $ssm->expiry_date = date('Y-m-d 00:00:00', time() + $trial_days * 86400);
                        $ssm->update();
                    }
                }
                if ($continueCheck) {
                    Yii::log("ko co trial");
                    if (isset($promotion)) {
                        if ($using_days > 0 && $channel != CHANNEL_TYPE_ADMIN) {
                            $ssm->expiry_date = date('Y-m-d 00:00:00', time() + $using_days * 86400);
                            $ssm->sent_notification = 1;
                            $ssm->update();
                        }
                    }
                }
            }
//            }else{
//                if(isset($trial)) {
//                    if($trial_days > 0) {
//                        $continueCheck = false;
//                        $ssm->expiry_date = date('Y-m-d 00:00:00', time() + $trial_days*86400);
//                        $ssm->update();
//                    }
//                }
//                if($continueCheck) {
//                    Yii::log("ko co trial");
//                    if(isset($promotion)) {
//                        if($using_days > 0) {
//                            $ssm->expiry_date = date('Y-m-d 00:00:00', time() + $using_days*86400);
//                            $ssm->sent_notification = 1;
//                            $ssm->update();
//                        }
//                    }
//                }
//            }

            $this->responseError($registerResult, $this->getRegisterErrorMsg($registerResult));
        } catch (Exception $e) {
            Yii::log("Exception: " . $e->getMessage());
            $this->responseError($registerResult, $this->getRegisterErrorMsg($registerResult));
        }
    }

    private function getUsingDaysByPromotion($promotion, $service)
    {
        $promotion = trim($promotion);
        /* @var $service Service */
        if (empty($promotion)) return 0;
        if (($promotion === 0) && (strlen($promotion) < 2)) {
            return $service->using_days;
        }
        //co dang Nc,Nd,Nw,Nm (N la so - c,d,w,m: chu ky, day, week, month
        $strlen = strlen($promotion);
        $suffix = $promotion[$strlen - 1];
        $num = intval(substr($promotion, 0, $strlen - 1));
        switch ($suffix) {
            case 'c':
                return $num * $service->using_days;
            case 'd':
                return $num;
            case 'w':
                return $num * 7;
            case 'm':
                $nextNumMonthTs = strtotime("+$num months", 0);
                return $nextNumMonthTs / 86400;
            default:
                return 0;
        }

        return 0;
    }

    public function responseError($error_no, $message)
    {
        header("Content-type: text/xml; charset=utf-8");
        $xmlDoc = new DOMDocument();
        $xmlDoc->encoding = "UTF-8";
        $xmlDoc->version = "1.0";

        //TODO: authen, session, error handle
        $root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
        $root->appendChild($xmlDoc->createElement("error_no", $error_no));
        $root->appendChild($xmlDoc->createElement("error_desc", CHtml::encode($message)));

        echo $xmlDoc->saveXML();
        Yii::app()->end();
    }

    public function responseAccountInfo($error_no, $message, $last_time_subscribe, $last_time_unsubscribe, $last_time_renew, $last_time_retry, $expire_time, $status, $packagename = NULL)
    {
        header("Content-type: text/xml; charset=utf-8");
        $xmlDoc = new DOMDocument();
        $xmlDoc->encoding = "UTF-8";
        $xmlDoc->version = "1.0";

        //TODO: authen, session, error handle
        $root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
        $root->appendChild($xmlDoc->createElement("status", $status));
        $root->appendChild($xmlDoc->createElement("error_no", $error_no));
        $root->appendChild($xmlDoc->createElement("error_desc", CHtml::encode($message)));
        if ($packagename != NULL) {
            $root->appendChild($xmlDoc->createElement("packagename", CHtml::encode($packagename)));
        }
        if (!isset($last_time_subscribe)) {
            $last_time_subscribe = 'NULL';
        }
        $root->appendChild($xmlDoc->createElement("last_time_subscribe", CHtml::encode($last_time_subscribe)));
        if (!isset($last_time_unsubscribe)) {
            $last_time_unsubscribe = 'NULL';
        }
        $root->appendChild($xmlDoc->createElement("last_time_unsubscribe", CHtml::encode($last_time_unsubscribe)));
        if (!isset($last_time_renew)) {
            $last_time_renew = 'NULL';
        }
        $root->appendChild($xmlDoc->createElement("last_time_renew", CHtml::encode($last_time_renew)));
        if (!isset($last_time_retry)) {
            $last_time_retry = 'NULL';
        }
        $root->appendChild($xmlDoc->createElement("last_time_retry", CHtml::encode($last_time_retry)));
        if (!isset($expire_time)) {
            $expire_time = 'NULL';
        }
        $root->appendChild($xmlDoc->createElement("expire_time", CHtml::encode($expire_time)));

        echo $xmlDoc->saveXML();
        Yii::app()->end();
    }

    private function getRegisterErrorMsg($error_no)
    {
        switch ($error_no) {
            case 0:
                return "Đăng ký thành công dịch vụ";
            case 1:
                return "Thuê bao này đã tồn tại";
            case 2:
                return "Đăng ký rồi và đăng ký lại dịch vụ";
            case 3:
                return "Đăng ký thành công dịch vụ và không bị trừ cước đăng ký";
            case 4:
                return "Đăng ký thành công dịch vụ và bị trừ cước đăng ký";
            case 5:
                return "Đăng ký không thành công do không đủ tiền trong tài khoản";
            default:
                return "Lỗi hệ thống - Đăng ký không thành công";
        }
    }

    private function getCancelErrorMsg($error_no)
    {
        switch ($error_no) {
            case 0:
                return "Hủy dịch vụ thành công";
            case 1:
                return "Thuê bao này chưa đăng ký gói cước";
            default:
                return "Lỗi hệ thống - Hủy không thành công";
        }
    }

    private function getCancelSubscriberErrorMsg($error_no)
    {
        switch ($error_no) {
            case 0:
                return "Hủy thuê bao thành công";
            case 1:
                return "Thuê bao này không tồn tại";
            default:
                return "Lỗi hệ thống - Hủy thuê bao không thành công";
        }
    }

    private function getChangeMsisdnErrorMsg($error_no)
    {
        switch ($error_no) {
            case 0:
                return "Đổi số thành công";
            case 1:
                return "Không tồn tại thuê bao A";
            case 2:
                return "Hủy thuê bao B không thành công";
            default:
                return "Lỗi hệ thống - Đổi số không thành công";
        }
    }

    private function getAccountInfoErrorMsg($error_no)
    {
        switch ($error_no) {
            case 0:
                return "Thuê bao không đăng ký sử dụng dịch vụ";
            case 1:
                return "Thuê bao đang sử dụng dịch vụ";
            case 2:
                return "Thuê bao không tồn tại";
            case 3:
                return "Không xác định";
            default:
                return "Lỗi hệ thống";
        }
    }

    private function getTransErrorMsg($error_no)
    {
        switch ($error_no) {
            case 0:
                return "Thành công";
            default:
                return "Lỗi hệ thống";
        }
    }

    private function getMTContent($action, $application, $isFirstUse = false, $day = 7, $subscriberId = null)
    { //tuy theo action & application goi api ma co noi dung MT khac nhau
        $times = false;
        $stardate = strtotime(date('Y-m-d 07:00:00'));
        $enddate = strtotime(date('Y-m-d 17:30:59'));
        if((strtotime(date('Y-m-d H:i:s')) > $stardate) && strtotime(date('Y-m-d H:i:s')) < $enddate){
            $times = true;
        }
        switch ($action) {
            case self::ACTION_REGISTER:
                switch ($application) {
// 					MOBILE_ADS hoặc VASPORTAL thì hoặc VASDEALER:
                    case self::ACTION_REGISTER_TEST:
                        if ($isFirstUse) {
                            if ($day == 1) {
                                if($times == true){
                                    $mtMessage = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms1 = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                                }else{
                                    $mtMessage = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms1 = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                                }
                            } else {
                                $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                            }
                        } else {
                            if($times == true){
                                $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms1 = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                            }else{
                                $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms1 = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                            }
                        }
                        break;
                    case self::APPLICATION_MOBILE_ADS:
                        if ($isFirstUse) {
                            if ($day == 1) {
                                if($times == true){
                                    $mtMessage = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms1 = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                                }else{
                                    $mtMessage = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms1 = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                                }
                            } else {
                                $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                            }
                        } else {
                            if($times == true){
                                $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms1 = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                            }else{
                                $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms1 = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                            }
                        }
                        break;
                    case self::APPLICATION_VASPORTAL:
                    case self::APPLICATION_VASDEALER:
                        if ($isFirstUse) {
                            if ($day == 1) {
                                $mtMessage = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay dau tien trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han.Quy khach vui long truy cap http://hocde.vn de thuong thuc hang nghin bo phim hap dan MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan Huy gui 1579.";
                            } else {
                                $mtMessage = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi $day ngay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han.Quy khach vui long truy cap http://hocde.vn de thuong thuc hang nghin bo phim hap dan MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan Huy gui 1579.";
                            }
                        } else {
                            $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan H gui 1579.";
                        }
                        break;
                    case self::APPLICATION_CCOS:
                        if ($isFirstUse) {
                            if ($day == 1) {
                                $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong dich vu vFilm qua he thong Cham soc khach hang cua VinaPhone. Quy Khach duoc mien phi ngay dau tien trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                            } else {
                                $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong dich vu vFilm qua he thong Cham soc khach hang cua VinaPhone. Quy Khach duoc mien phi $day ngay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                            }
                        } else {
                            $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong dich vu vFilm qua he thong Cham soc khach hang cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han. De huy dich vu, Quy Khach vui long soan HUY gui 1579.";
                        }
                        break;
                    default:
                        if ($isFirstUse) {
                            if ($day == 1) {
                                if($times == true){
                                    $mtMessage = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms1 = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                                }else{
                                    $mtMessage = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms1 = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. Quy khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan HUY gui 1579.";
                                    $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                                }
                            } else {
                                $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                            }
                        } else {
                            if($times == true){
                                $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms1 = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                            }else{
                                $mtMessage = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms1 = "Chuc mung Quy Khach da dang ky thanh cong goi dich vu vFilm cua VinaPhone. Quy Khach vui long truy cap http://hocde.vn/ de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han hang tuan. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
                                $delaySms2 = "Quy khach dang duoc xem hang ngan bo phim hanh dong dac sac, phim tam ly tinh cam, phim hai vui nhon, phim kiem hiep, phim vo thuat, phim kinh dien va nhung bo phim an khach nhat 2014 HOAN TOAN MIEN PHI CUOC 3G/GPRS. Truy cap http://hocde.vn de xem ngay.";
                            }
                        }
                        break;
                }
                break;
            case self::ACTION_CANCEL:
                $mtMessage = "Quy Khach da huy thanh cong dich vu vFilm. De dang ky lai dich vu, Quy Khach vui long soan DK gui 1579. Cam on Quy Khach da su dung dich vu cua VinaPhone.";
                break;
        }
        if($subscriberId == null){//check ảo để ko delay sms nua.
            if(isset($delaySms1) && isset($delaySms2)){
                if($subscriberId != null){
                    $inse = SmsQueue::insertDelaySms($subscriberId, $delaySms1);
                    $inse = SmsQueue::insertDelaySms($subscriberId, $delaySms2);
                }
            }
        }
        return $mtMessage;
    }

// 	3.3
// 	http://hocde.vn/api/telco/cancelService?requestid=2&msisdn=84916731158&note=vnp&application=vnp_api&channel=api&username=chau&userip=127.0.0.1
    public function actionCancelService($requestid = null, $msisdn = null, $packagename = VFILM, $policy = null, $promotion = null, $note = null, $application = 'TEST', $channel = 'API', $username = null, $userip = null)
    {
        $cancelResult = 101;
        $filelog = '/tmp/trungdh.log';
        $infomation = 'Time:' . date('Y/m/d H:i:s') . '-';
        $infomation .= "CancelService:reqid:$requestid|msisdn:$msisdn|package:$packagename|policy:$policy|promotion:$promotion|note:$note|app:$application|channel:$channel|";

        try {
            $msisdn = CUtils::validatorMobile($msisdn);
            $subscriber = Subscriber::model()->findByAttributes(array('subscriber_number' => $msisdn));
            /* @var $subscriber Subscriber */
            if ($subscriber == NULL) {
                $cancelResult = 1;
                $infomation .= "Thue bao $msisdn khong ton tai, err: $cancelResult";
                file_put_contents($filelog, $infomation . "\r\n", FILE_APPEND | LOCK_EX);
                throw new Exception("Loi he thong - thue bao $msisdn khong ton tai");
            }

            $ssm = $subscriber->getUsingService();
            if ($ssm == NULL) {
                if (isset($note)) {
                    $mo = explode('|', $note);
                    if ($mo[0] == 'MO' && $ssm == NULL) {
                        $mtMessage = 'Quy Khach chua dang ky dich vu vFilm cua VinaPhone. De dang ky dich vu, Quy Khach vui long soan DK gui 1579. De biet them chi tiet, Quy Khach vui long lien he tong dai 9191.';
                        $res = VinaphoneController::sendSms($msisdn, $mtMessage);
                        $aDate = new DateTime();
                        $mt = new SmsMessage();
                        $mt->type = $mo[0];
                        $mt->source = $mo[3];
                        $mt->destination = $mo[2];
                        $mt->message = $mo[1];
                        $mt->received_time = $aDate->format('Y-m-d H:i:s');
                        $mt->sending_time = $aDate->format('Y-m-d H:i:s');
                        if (isset($response) && isset($response->body)) {
                            $mt->mt_status = $response->body;
                        }
                        if (isset($subscriber))
                            $mt->subscriber_id = $subscriber->id;
                        $mt->save();
                    }
                }
                $cancelResult = 1;
                $infomation .= "Thue bao $msisdn chua dang ky dich vu, err: $cancelResult";
                file_put_contents($filelog, $infomation . "\r\n", FILE_APPEND | LOCK_EX);
                throw new Exception("Thue bao chua dang ky dich vu");
            }
            $description = "_app_$application" . "_trial_0_note_$note";
            $chargingResult = $subscriber->cancelService($ssm, $channel, $requestid, $description, $username, $userip);
            if ($chargingResult == CPS_OK || ($chargingResult == '11') || ($chargingResult == '6')) {
//                if ($subscriber->id == 491006 || $subscriber->id == 240) {
                if (isset($note)) {
                    $mo = explode('|', $note);
                    if ($mo[0] == 'MO') {
                        $aDate = new DateTime();
                        $mt = new SmsMessage();
                        $mt->type = $mo[0];
                        $mt->source = $mo[3];
                        $mt->destination = $mo[2];
                        $mt->message = $mo[1];
                        $mt->received_time = $aDate->format('Y-m-d H:i:s');
                        $mt->sending_time = $aDate->format('Y-m-d H:i:s');
                        if (isset($response) && isset($response->body)) {
                            $mt->mt_status = $response->body;
                        }
                        if (isset($subscriber))
                            $mt->subscriber_id = $subscriber->id;
                        $mt->save();
                    }
                }
//                }
                $mtMessage = $this->getMTContent(self::ACTION_CANCEL, $application);
                $res = VinaphoneController::sendSms($msisdn, $mtMessage);
                $cancelResult = 0;
            }
            $infomation .= "chargingResult:$chargingResult|err:$cancelResult";
            file_put_contents($filelog, $infomation . "\r\n", FILE_APPEND | LOCK_EX);
            $this->responseError($cancelResult, $this->getCancelErrorMsg($cancelResult));
        } catch (Exception $e) {
            $this->responseError($cancelResult, $this->getCancelErrorMsg($cancelResult));
        }
    }

// 	3.4 Lấy thông tin gói dịch vụ
// 	http://hocde.vn/api/telco/getSubscriberInfo?requestid=1&msisdn=84916731158&packagename=VFILM&note=vnp&application=vnp_api&channel=api&username=chau&userip=127.0.0.1
    public function actionGetSubscriberInfo($requestid = null, $msisdn = null, $packagename = VFILM, $application = 'TEST', $channel = 'API', $username = null, $userip = null)
    {
        $this->getSubscriberInfo($requestid, $msisdn, $packagename, $application, $channel, $username, $userip);
    }

// 	3.5 Lấy thông tin tất cả gói dịch vụ
// 	http://hocde.vn/api/telco/getSubscriberAllPackageInfo?requestid=1&msisdn=84916731158&note=vnp&application=vnp_api&channel=api&username=chau&userip=127.0.0.1
    public function actionGetSubscriberAllPackageInfo($requestid = null, $msisdn = null, $application = 'TEST', $channel = 'API', $username = null, $userip = null)
    {
        $this->getSubscriberInfo($requestid, $msisdn, $packagename, $application, $channel, $username, $userip, 'VFILM');
    }

    public function getSubscriberInfo($requestid, $msisdn, $packagename = VFILM, $application = 'TEST', $channel = 'API', $username = null, $userip = null, $packagenameToShow = NULL)
    {
        $getInfoResult = 110;
        $last_time_subscribe = NULL;
        $last_time_unsubscribe = NULL;
        $last_time_renew = NULL;
        $last_time_retry = NULL;
        $expire_time = NULL;
        $status = 0;
        try {
            $msisdn = CUtils::validatorMobile($msisdn);
            $subscriber = Subscriber::model()->findByAttributes(array('subscriber_number' => $msisdn));
            /* @var $subscriber Subscriber */
            if ($subscriber == NULL) {
                $getInfoResult = 0;
                $status = 2;
                throw new Exception("Loi he thong - thue bao $msisdn khong ton tai");
            }

            if ($subscriber->status == 2) { //thue bao nay da bi huy (huy thue bao, ko phai huy dich vu)
                $getInfoResult = 0;
                $status = 2;
                throw new Exception("Loi he thong - thue bao $msisdn khong ton tai");
            }

// 			$ssm = ServiceSubscriberMapping::model()->findBySql("select * from service_subscriber_mapping where subscriber_id = ".$subscriber->id." order by id desc limit 1");
            $ssm = ServiceSubscriberMapping::model()->findByAttributes(array("subscriber_id" => $subscriber->id), array("order" => "id desc")); //Sql("select * from service_subscriber_mapping where subscriber_id = ".$subscriber->id." order by id desc limit 1");
            if ($ssm == NULL) {
                $getInfoResult = 0;
                $status = 2;
                throw new Exception("Loi he thong - thue bao $msisdn khong ton tai");
            }

            $create_date_ts = strtotime($ssm->create_date);
            $modify_date_ts = strtotime($ssm->modify_date);
            $expire_time_ts = strtotime($ssm->expiry_date);

            $lastSuccessExtendTrans = SubscriberTransaction::model()->findByAttributes(array('subscriber_id' => $subscriber->id, 'purchase_type' => PURCHASE_TYPE_RECUR, 'status' => 1), array('order' => 'id desc'));
            $lastFailedExtendTrans = SubscriberTransaction::model()->findByAttributes(array('subscriber_id' => $subscriber->id, 'purchase_type' => PURCHASE_TYPE_RECUR, 'status' => 2), array('order' => 'id desc'));
            $lastSubscribeTrans = SubscriberTransaction::model()->findByAttributes(array('subscriber_id' => $subscriber->id, 'purchase_type' => PURCHASE_TYPE_NEW, 'status' => 1, 'service_id' => '5'), array('order' => 'id desc'));
// 			$lastUnSubscribeTrans = SubscriberTransaction::model()->findByAttributes(array('subscriber_id' => $subscriber->id, array('condition' => 'purchase_type' == PURCHASE_TYPE_CANCEL OR 'purchase_type' == PURCHASE_TYPE_FORCE_CANCEL), 'status' => 1, 'service_id' => '5'), array('order' => 'id desc'));
            $lastUnSubscribeTrans = SubscriberTransaction::model()->findBySql("select * from subscriber_transaction where subscriber_id = " . $subscriber->id . " and (purchase_type = 3 or purchase_type = 4) and status = 1 order by id desc limit 1");
            if ($lastSubscribeTrans != NULL) {
                $last_time_subscribe = date('YmdHis', strtotime($lastSubscribeTrans->create_date));
            }
            if ($lastUnSubscribeTrans != NULL) {
                $last_time_unsubscribe = date('YmdHis', strtotime($lastUnSubscribeTrans->create_date));
            }
            if ($lastSuccessExtendTrans != NULL) { //khong no cuoc
                $last_time_renew = date('YmdHis', strtotime($lastSuccessExtendTrans->create_date));
            }
            if ($lastFailedExtendTrans != NULL) { //khong no cuoc
                $last_time_retry = date('YmdHis', strtotime($lastFailedExtendTrans->create_date));
            }

            if ($ssm->is_active == 1) {
                $getInfoResult = 0;
                $status = 1;
                $expire_time = date('YmdHis', $expire_time_ts);
                throw new Exception("Thue bao dang dung dich vu");
            } else {
                $getInfoResult = 0;
                $status = 0;
                throw new Exception("Thue bao chua dang ky dich vu");
            }

            $getInfoResult = 110;
            throw new Exception("Loi he thong");
        } catch (Exception $e) {
            Yii::log("Exception: " . $e->getMessage());
            $this->responseAccountInfo($getInfoResult, $this->getAccountInfoErrorMsg($status), $last_time_subscribe, $last_time_unsubscribe, $last_time_renew, $last_time_retry, $expire_time, $status, $packagenameToShow);
        }
    }

// 	3.8 Nghiệp vụ hủy thuê bao
// 	http://hocde.vn/api/telco/CancelSubscriber?requestid=1&msisdn=84916731158&application=vnp_api&channel=api&username=chau&userip=127.0.0.1
    public function actionCancelSubscriber($requestid = null, $msisdn = null, $reason = 'Hủy', $application = 'TEST', $channel = 'API', $username = null, $userip = null)
    {
        $cancelResult = 110;
        $filelog = '/tmp/trungdh.log';
        $infomation = 'actionCancelSubscriber Time:' . date('Y/m/d H:i:s') . '-';
        $infomation .= "CancelSubs:reqid:$requestid|msisdn:$msisdn|reason:$reason|app:$application|channel:$channel|";
        try {
            $msisdn = CUtils::validatorMobile($msisdn);
            $subscriber = Subscriber::model()->findByAttributes(array('subscriber_number' => $msisdn, 'status' => 1));
            /* @var $subscriber Subscriber */
            if ($subscriber == NULL) {
                $cancelResult = 1;
                $infomation .= "Thue bao $msisdn khong ton tai, err: $cancelResult";
                file_put_contents($filelog, $infomation . "\r\n", FILE_APPEND | LOCK_EX);
                throw new Exception("Thue bao $msisdn khong ton tai");
            }
            $ssm = $subscriber->getUsingService();
            if ($ssm != NULL) {
                $description = "_app_$application" . "_trial_0_note_$reason";
                $chargingResult = $subscriber->cancelService($ssm, $channel, $requestid, $description, $username, $userip);
//$infomation .= "Thue bao $msisdn - chargingResult: $chargingResult, err: $cancelResult";
                $infomation .= "chargingResult:$chargingResult|err:$cancelResult";
                file_put_contents($filelog, $infomation . "\r\n", FILE_APPEND | LOCK_EX);
                if ($chargingResult == CPS_OK || ($chargingResult == '11') || ($chargingResult == '6')) {
                    $mtMessage = $this->getMTContent(self::ACTION_CANCEL, $application);
                    //$res = VinaphoneController::sendSms($msisdn, $mtMessage);
// 					$cancelResult = 0;
// 					throw new Exception("Huy thanh cong");
                } else {
// 					$cancelResult = 110;
                    throw new Exception("Loi he thong");
                }
            }
            $subscriber->status = 2;
            $subscriber->update();
            $cancelResult = 0;
            $this->responseError($cancelResult, $this->getCancelSubscriberErrorMsg($cancelResult));
        } catch (Exception $e) {
            $this->responseError($cancelResult, $this->getCancelSubscriberErrorMsg($cancelResult));
        }
    }

// 	3.9 Nghiệp vụ chuyển số thuê bao
// 	http://hocde.vn/api/telco/ChangeMsisdn?requestid=1&msisdnA=84916731158&msisdnB=84916731159&application=vnp_api&channel=api&username=chau&userip=127.0.0.1
    public function actionChangeMsisdn($requestid = null, $msisdnA = null, $msisdnB = null, $reason = 'Đổi số', $application = 'TEST', $channel = 'API', $username = null, $userip = null)
    {
        $changeMsisdnResult = 110;
        try {
            $msisdnA = CUtils::validatorMobile($msisdnA);
            $subscriberA = Subscriber::model()->findByAttributes(array('subscriber_number' => $msisdnA));
            /* @var $subscriberA Subscriber */
            /* @var $subscriberB Subscriber */
            if ($subscriberA == NULL) {
                $changeMsisdnResult = 1;
                throw new Exception("Thue bao $msisdnA khong ton tai");
            }
            $ssm = $subscriberA->getUsingService();
            if ($ssm == NULL) {
                $changeMsisdnResult = 1;
                throw new Exception("Thue bao $msisdnA khong ton tai");
            }

            $msisdnB = CUtils::validatorMobile($msisdnB);
            if (!isset($msisdnB)) {
                $changeMsisdnResult = 101;
                throw new Exception("So dien thoai $msisdnB khong hop le");
            }
            $subscriberB = Subscriber::model()->findByAttributes(array('subscriber_number' => $msisdnB));
            /* @var $subscriber Subscriber */
            if ($subscriberB != NULL) {
                $ssm = $subscriberB->getUsingService();
                if ($ssm != NULL) {
                    $description = "_app_$application" . "_trial_0_note_$reason";
                    $chargingResult = $subscriberB->cancelService($ssm, $channel, $requestid, $description, $username, $userip);
                    if ($chargingResult == CPS_OK) {
                    } else {
                        $changeMsisdnResult = 2; //huy dich vu cua thue bao msisdnB ko thanh cong
                        throw new Exception("Loi he thong");
                    }
                }
                $subscriberB->subscriber_number = $subscriberB->subscriber_number . "_disabled";
                $subscriberB->status = 2;
                $subscriberB->update();
            }

            $subscriberA->subscriber_number = $msisdnB;
            if ($subscriberA->update()) {
                $changeMsisdnResult = 0;
            }
            $this->responseError($changeMsisdnResult, $this->getChangeMsisdnErrorMsg($changeMsisdnResult));
        } catch (Exception $e) {
            $this->responseError($changeMsisdnResult, $this->getChangeMsisdnErrorMsg($changeMsisdnResult));
        }
    }

// 	http://hocde.vn/api/telco/GetLastAction?requestid=1&msisdn=84916731158&note=vnp&application=vnp_api&channel=api&username=chau&userip=127.0.0.1&fromdate=20131110&todate=20131122
// 	3.10 Kiểm tra sự tương tác/sử dụng của khách hàng với dịch vụ
    public function actionGetLastAction($requestid = null, $msisdn = null, $packagename = VFILM, $fromdate = NULL, $todate = NULL, $application = 'TEST', $channel = 'API', $username = null, $userip = null)
    {
        $getLastActionResult = 110;
        $lastViewTime = 0;
        $lastTransTime = 0;
        $lastActionTime = 0;
        $status = 0;
        $channel_type = '';
        $description = '';
        try {
            $fDateTs = strtotime($fromdate);
            $tDateTs = strtotime($todate);

            $msisdn = CUtils::validatorMobile($msisdn);
            $subscriber = Subscriber::model()->findByAttributes(array('subscriber_number' => $msisdn));
            /* @var $subscriber Subscriber */
            if ($subscriber == NULL) {
                $getLastActionResult = 101;
                throw new Exception("Thue bao $msisdn khong ton tai");
            }


            $lastVodView = StreamingLog::model()->findBySql("select * from streaming_log where subscriber_id = $subscriber->id order by id desc limit 1");
            /* @var $lastVodView StreamingLog */
            if ($lastVodView != NULL) {
                $lastViewTime = strtotime($lastVodView->create_date);
                if (($fDateTs < $lastViewTime) && ($tDateTs > $lastViewTime)) {
                    //thoa man la action nam trong khoang thgian can check
                } else {
                    $lastViewTime = 0;
                }
            }

            $lastTransaction = SubscriberTransaction::model()->findBySql("select * from subscriber_transaction where subscriber_id = $subscriber->id and status = 1 order by id desc limit 1");
            if ($lastTransaction != NULL) {
                $lastTransTime = strtotime($lastTransaction->create_date);
                if (($fDateTs < $lastTransTime) && ($tDateTs > $lastTransTime)) {
                    //thoa man la action nam trong khoang thgian can check
                } else {
                    $lastTransTime = 0;
                }
            }

            if (($lastTransTime == 0) && ($lastViewTime == 0)) {
                //ko co action nao trong khoang thgian nay
                $getLastActionResult = 0;
                throw new Exception("success");
            }

            $status = 1;
            if ($lastViewTime > $lastTransTime) {
                $channel_type = $lastVodView->channel_type;
                $description = 'Xem phim: ' . $lastVodView->getVodName();
                $lastActionTime = $lastViewTime;
            } else {
                $channel_type = $lastTransaction->channel_type;
                $description = SubscriberTransaction::getTypeName($lastTransaction->purchase_type);
                if ($lastTransaction->service_id != NULL) {
                    $description .= " gói cước";
                } else {
                    $description .= " phim";
                }
                $lastActionTime = $lastTransTime;
            }
            $getLastActionResult = 0;
            throw new Exception("success");
        } catch (Exception $e) {
            $this->responseLastAction($getLastActionResult, $this->getLastActionErrorMsg($getLastActionResult), $status, $description, $lastActionTime, $channel_type);
        }
    }

    private function getLastActionErrorMsg($error_no)
    {
        switch ($error_no) {
            case 0:
                return "Thành công";
            case 101:
                return "Thuê bao không tồn tại";
            default:
                return "Thất bại";
        }
    }

    private function responseLastAction($error_no, $message, $status, $description, $lastActionTime, $channel_type)
    {
        header("Content-type: text/xml; charset=utf-8");
        $xmlDoc = new DOMDocument();
        $xmlDoc->encoding = "UTF-8";
        $xmlDoc->version = "1.0";

        //TODO: authen, session, error handle
        $root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
        $root->appendChild($xmlDoc->createElement("error_no", $error_no));
        $root->appendChild($xmlDoc->createElement("error_desc", CHtml::encode($message)));
        $root->appendChild($xmlDoc->createElement("status", $status));
        if (!isset($lastActionTime)) {
            $lastActionTime = 'NULL';
        }
        $root->appendChild($xmlDoc->createElement("lastActionTime", CHtml::encode(date('YmdHis', $lastActionTime))));
        if (!isset($channel_type)) {
            $channel_type = 'NULL';
        }
        $root->appendChild($xmlDoc->createElement("last_channel", CHtml::encode($channel_type)));
        if (!isset($description)) {
            $description = 'NULL';
        }
        $root->appendChild($xmlDoc->createElement("description", CHtml::encode($description)));

        echo $xmlDoc->saveXML();
        Yii::app()->end();
    }
}

<?php
class ChargingProxy {
	const ACTION_REGISTER = 1;
	const ACTION_CANCEL = 2;
	const ACTION_EXTEND_TIME = 3;
	const ACTION_EXTEND_SERVICE = 4;

	const PROCESS_ERROR_NONE = 0;
	const PROCESS_ERROR_NULL_SUBSCRIBER = 1;
	const PROCESS_ERROR_USING_SERVICE = 2; //dang dung dich vu roi ma con register service
	const PROCESS_ERROR_NOT_USING_SERVICE = 3; //chua dung dich vu nao ma muon cancel service
	const PROCESS_ERROR_NOT_ENOUGH_MONEY = 4;
	const PROCESS_ERROR_GENERAL = 5;
	const PROCESS_ERROR_INVALID_SERVICE = 6;
	const PROCESS_ERROR_INVALID_PHONENUMBER = 7;
	const PROCESS_ERROR_INTERNAL_SYSTEM = 8;
	const PROCESS_ERROR_CODE_INVALID = 9;
	const PROCESS_ERROR_GIFT_EXPIRED = 10;
	const PROCESS_ERROR_INVALID_VOD = 11;
	const PROCESS_ERROR_FREE_VOD = 12; //video nay free, ko can mua
	const PROCESS_ERROR_BOUGHT_VOD = 13; //video nay mua le roi, ko can mua nua

	const CHARGING_CONTENT_CP_NAME = "CP_NET2E";
	const CHARGING_CONTENT_REGISTER = "CP_NET2E|dang ky goi|";
	const CHARGING_CONTENT_EXTEND = "CP_NET2E|gia han goi|";
	const CHARGING_CONTENT_CANCEL = "CP_NET2E|huy goi|";

	const CHARGING_REGISTER = "REG_WEEKLY"; //dang ky goi HD, HD7
	const CHARGING_EXTEND_SERVICE = "RENEW_WEEKLY"; //gia han dich vu goi HD7
	const CHARGING_CANCEL = "UNREG_WEEKLY"; //huy dich vu
	const CHARGING_CONTENT = "CONTENT"; // mua cau hoi le
	

	public static function chargingPayVod($msisdn, $vod, $transaction_id, $promotion=false, $debit_amount = -1, $channel = CHANNEL_TYPE_WAP) {
		$msisdn = CUtils::validatorMobile($msisdn,0);
		$command = self::CHARGING_CONTENT;
		$content = "";
		if($debit_amount == -1) {
			$debit_amount = intval($vod->price);
		}
		$original_price = intval($vod->price);
		$response = Vinaphone::charging($transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $content, $channel);
		Yii::log("chargingPayVod: error_code = " . $response->return);
		return $response->return;
	}
	
	public static function chargingRegister($username, $userip, $msisdn, $service, $transaction_id, $promotion=false, $debit_amount = -1, $channel = CHANNEL_TYPE_WAP, $note=NULL) { //debit_amount == -1 -> == $service->price (truong hop promotion==0)
		$msisdn = CUtils::validatorMobile($msisdn,0);
		$command = self::CHARGING_REGISTER;
		if($note == NULL) {
			$content = $service->code_name;
		}
		else {
			$content = $note;
		}
		$original_price = intval($service->price);
		if($debit_amount == -1) {
			$debit_amount = $original_price;
		}
		$response = Vinaphone::charging2($username, $userip, $transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $content, $channel);
		Yii::log("chargingRegister: error_code = " . $response->return);
		return $response->return;
	}

	 public static function chargingPromotionRegister($username, $userip, $msisdn, $service, $transaction_id, $debit_amount = 0, $channel = CHANNEL_TYPE_WAP, $note = NULL) { //debit_amount == -1 -> == $service->price (truong hop promotion==0)
	 	$msisdn = CUtils::validatorMobile($msisdn,0);
	 	$command = self::CHARGING_REGISTER;
	 	if($note == NULL) {
	 		$content = "FREE_FIRST_CYCLE";
	 	}
	 	else {
	 		$content = $note;
	 	}
	 	$promotion=true;
	 	$original_price = intval($service->price);
	 	if($debit_amount == -1) {
	 		$debit_amount = $original_price;
	 	}
	 	$response = Vinaphone::charging2($username, $userip, $transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $content, $channel);
	 	Yii::log("chargingRegister: error_code = " . $response->return);
	 	return $response->return;
	 }

	public static function chargingRegister2($msisdn, $service, $transaction_id, $promotion=false, $debit_amount = -1, $channel = CHANNEL_TYPE_WAP, $note=NULL) { //debit_amount == -1 -> == $service->price (truong hop promotion==0)
		$msisdn = CUtils::validatorMobile($msisdn,0);
		$command = self::CHARGING_REGISTER;
		if($note == NULL) {
			$content = $service->code_name;
		}
		else {
			$content = $note;
		}
		$original_price = intval($service->price);
		if($debit_amount == -1) {
			$debit_amount = $original_price;
		}
		$response = Vinaphone::charging($transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $command, $content, $channel);
		Yii::log("chargingRegister: error_code = " . $response->return);
		return $response;
	}

	public static function chargingExtendService($msisdn, $service, $transaction_id, $channel = CHANNEL_TYPE_WAP) {
		$msisdn = CUtils::validatorMobile($msisdn,0);
		$command = self::CHARGING_EXTEND_SERVICE;
		$content = $service->code_name;
		$debit_amount = $original_price = intval($service->price);
		$response = Vinaphone::charging($transaction_id, $msisdn, false, $debit_amount, $original_price, $command, $content, $channel);
		Yii::log("chargingExtendService: error_code = " . $response->return);
		return $response->return;
	}

	public static function chargingCancel($username, $userip, $msisdn, $service, $transaction_id, $channel = 'WAP') {
		$msisdn = CUtils::validatorMobile($msisdn,0);
		$command = self::CHARGING_CANCEL;
		$content = $service->code_name;
		$response = Vinaphone::charging2($username, $userip, $transaction_id, $msisdn, false, 0, 0, $command, $content, $channel);
		Yii::log("chargingCancel: error_code = " . $response->return);
		return $response->return;
	}

	public static function processPayVod($channel_type, $vod, $subscriber) {
		/* @var $subscriber Subscriber */
		$createTime = date('Y-m-d H:i:s', time());
		
		$usingServices = $subscriber != NULL ?
		ServiceSubscriberMapping::model()->findByAttributes(array('subscriber_id' => $subscriber->id, 'is_active' => 1)):null;
		
		$mtMessage = "";
		if($subscriber == NULL) {
			return self::PROCESS_ERROR_NULL_SUBSCRIBER;
		}
		$msisdn = $subscriber->subscriber_number;
		
		if($usingServices != NULL) {
			return self::PROCESS_ERROR_USING_SERVICE;
		}
		
		if($vod == NULL) {
			return self::PROCESS_ERROR_INVALID_VOD;
		}
		
		if($vod->is_free == 1) {
			return self::PROCESS_ERROR_FREE_VOD;
		}
		if($subscriber->hasVodAsset($vod, USING_TYPE_WATCH)) {
			return self::PROCESS_ERROR_BOUGHT_VOD;
		}
		
		$subscriberTransaction = $subscriber->newTransaction($channel_type, USING_TYPE_WATCH, SubscriberTransaction::PURCHASE_TYPE_NEW, null, $vod);
		if($subscriber->status == Subscriber::STATUS_WHITE_LIST) { //xu ly truong hop white_list
			$chargingResult = CPS_OK;
		}
		else {
			$chargingResult = ChargingProxy::chargingPayVod($msisdn, $vod, $subscriberTransaction->id, false, intval($subscriberTransaction->cost), $channel_type);
		}
		if($chargingResult == CPS_OK) {
			$subscriber->addVodAsset($vod, USING_TYPE_WATCH);
			$subscriberTransaction->status = 1;
			$subscriberTransaction->error_code = $chargingResult;
			$subscriberTransaction->update();
			return self::PROCESS_ERROR_NONE;
		}
		else if($chargingResult == NOK_NO_MORE_CREDIT_AVAILABLE) {//thuong ko giao dich thanh cong do loi nay: loi thieu tien
			$subscriberTransaction->status = 2;
			$subscriberTransaction->error_code = $chargingResult;
			$subscriberTransaction->update();
			return self::PROCESS_ERROR_NOT_ENOUGH_MONEY;
		}
		return self::PROCESS_ERROR_GENERAL;
	}
	
	/**
	 *
	 * @param unknown $channel_type == SMS -> xu ly chargingSms, else (wap, gia han, dky truc tiep) ko xu ly chargingSms
	 * @param unknown $content
	 */
	public static function processSubService($channel_type, $action, $service, $subscriber, $ssm = null, $user_name=NULL, $user_ip=NULL) {
		/* @var $ssm ServiceSubscriberMapping */
		$codeResult = -1; //chua set codeResult
		// loading subscriber info
		$createTime = date('Y-m-d H:i:s', time());

		$usingServices = $subscriber != null ?
		ServiceSubscriberMapping::model()->findByAttributes(array('subscriber_id' => $subscriber->id, 'is_active' => 1)):null;

		$mtMessage = "";
        $delaySms = "";
		if($subscriber == NULL) {
			return self::PROCESS_ERROR_NULL_SUBSCRIBER;
		}
		$msisdn = $subscriber->subscriber_number;

		if($action == self::ACTION_REGISTER) {
			if ($usingServices != null) { //dang co ssm active, thong bao loi da dung d/vu roi
				return self::PROCESS_ERROR_USING_SERVICE;
			} else {
				if (isset($service)) {
					//fix kich ban 1day km
					$freeFor1day = false;
					$isTestNumber = false;
					if ($subscriber->id == 240 || $subscriber->id == 265){
						$isTestNumber = true;
					}
					//end boolean fix
					$freeFor1stTime = false;
					$freeFor1stTimeByVnp = false;
					Yii::log("newTransaction processSubService charging proxy".$subscriber->id ."\n");
					$subscriberTransaction = $subscriber->newTransaction($channel_type, USING_TYPE_REGISTER, SubscriberTransaction::PURCHASE_TYPE_NEW, $service, null, null , $user_name, $user_ip);
					if($subscriber->status == Subscriber::STATUS_WHITE_LIST) { //xu ly truong hop white_list
						$chargingResult = CPS_OK;
					}
					else {
						if($isTestNumber){
							$isRegisteredInThreeMonth = ServiceSubscriberMapping::model()->isRegisteredInThreeMonth($subscriber->id);
							$freeFor1day = true;
							$subscriberTransaction->cost = 0;
							$chargingResult = ChargingProxy::chargingPromotionRegister($msisdn, $service, $subscriberTransaction->id);
						} elseif(count($subscriber->serviceSubscriberMappings) == 0 || (!(ServiceSubscriberMapping::isRegisteredInThreeMonth($subscriber->id)))) {
                            $freeFor1day = true;
//							$freeFor1stTime = true;
							$subscriberTransaction->cost = 0;
							$chargingResult = ChargingProxy::chargingPromotionRegister($msisdn, $service, $subscriberTransaction->id);
						}else{
							$c_response = ChargingProxy::chargingRegister2($msisdn, $service, $subscriberTransaction->id, false, intval($subscriberTransaction->cost), $channel_type);
							$chargingResult = $c_response->return;
							if (isset($c_response->promotion) && isset($c_response->note) && isset($c_response->price) &&
							 $c_response->promotion == '1' && $c_response->price=='0' && $c_response->note == 'BIG032014' ) 
								$freeFor1stTimeByVnp=true;
							//$chargingResult = ChargingProxy::chargingRegister($msisdn, $service, $subscriberTransaction->id, false, intval($subscriberTransaction->cost), $channel_type);
						}
					}
					$isChargingSuccess = false;
					if($chargingResult == CPS_OK) {
						$isChargingSuccess = true;
					}
					else if($chargingResult == NOK_NO_MORE_CREDIT_AVAILABLE) {//thuong ko giao dich thanh cong do loi nay: loi thieu tien
						if($channel_type != CHANNEL_TYPE_SMS) {
							$codeResult = self::PROCESS_ERROR_NOT_ENOUGH_MONEY;
						}
						// 							$mtMessage = "Quy khach dang ky goi ".$service->display_name." cua dich vu xem phim truc tuyen khong thanh cong do tai khoan khong du tien (".intval($service->price)."d/".$service->using_days."ngay). Quy khach vui long nap them tien va thao tac lai. Tran trong cam on!";
						$mtMessage = "Tai khoan cua Quy Khach da khong du de dang ky dich vu vFilm. Quy Khach vui long nap them tien vao tai khoan va thu lai. Xin tran trong cam on.";
					}
					else { //giao dich ko thanh cong do loi khac (ko phai thieu tien)
						if($channel_type != CHANNEL_TYPE_SMS) {
							$codeResult = self::PROCESS_ERROR_GENERAL;
						}
						$mtMessage = "Yeu cau cua Quy Khach chua duoc thuc hien do he thong dang ban. Xin Quy Khach vui long dang ky lai sau. Tran trong cam on.";
					}

					if($isChargingSuccess) {
						$subscriberTransaction->status = 1;
						$subscriberService = $subscriber->addService($service,null,USING_TYPE_REGISTER,$createTime);
						if($freeFor1day) {
							$subscriberService->expiry_date = date('Y-m-d 23:59:59');
							$subscriberService->update();
                            $mtMessage = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi ngay hom nay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han.Quy khach vui long truy cap http://vfilm.vn de thuong thuc hang nghin bo phim hap dan, MIEN PHI CUOC 3G/GPRS. De huy dich vu, Quy khach vui long soan Huy gui 1579.";
//							$mtMessage = "Chuc mung Quy Khach da dang ky thanh cong va duoc mien phi ngay hom nay dich vu vFilm cua VinaPhone, MIEN PHI CUOC 3G/GPRS.";
                            $delaySms = "Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
							$subscriberTransaction->cost = 0;
							$subscriberTransaction->description = "Miễn phí đăng ký ngày đầu";
						}
						elseif($freeFor1stTime) {
							$mtMessage = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi 7 ngay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han.Quy khach vui long truy cap http://vfilm.vn de thuong thuc hang nghin bo phim hap dan. De huy dich vu, Quy khach vui long soan Huy gui 1579.";
							$subscriberTransaction->cost = 0;
							$subscriberTransaction->description = "Miễn phí đăng ký lần đầu";
						}
						else {
							if ($freeFor1stTimeByVnp){
								$mtMessage = "Chuc mung Quy khach da dang ky thanh cong dich vu xem phim tren di dong vFilm cua VinaPhone. Quy khach duoc mien phi 7 ngay trai nghiem dich vu vFilm. Sau khi het thoi han mien cuoc, VinaPhone se tinh cuoc dich vu 10.000d/tuan va dich vu se duoc tu dong gia han.Quy khach vui long truy cap http://vfilm.vn de thuong thuc hang nghin bo phim hap dan. De huy dich vu, Quy khach vui long soan Huy gui 1579.";
								$subscriberTransaction->cost = 0;
								$subscriberTransaction->description = "Miễn phí ĐK lần đầu bởi VNP";
							} else {
								$mtMessage = "Chuc mung Quy Khach da dang ky thanh cong dich vu vFilm cua VinaPhone, MIEN PHI CUOC 3G/GPRS. Quy Khach vui long truy cap http://vfilm.vn/ de thuong thuc hang nghin bo phim hap dan.";
                                $delaySms = "Quy Khach se duoc tan huong nhung noi dung hap dan chat luong HD voi Gia cuoc 10.000d/tuan, dich vu se duoc tu dong gia han. De huy dich vu, Quy Khach vui long soan Huy gui 1579.";
							}
						}
						$subscriberTransaction->error_code = CPS_OK;
					}
					else {
						$subscriberTransaction->error_code = $chargingResult;
						$subscriberTransaction->status = 2;
					}
					
					$subscriberTransaction->create_date = $createTime;
					$subscriberTransaction->update();
					Yii::log("chargingResult : $chargingResult");
					if($codeResult > 0) { //neu codeResult > -1 ko gui MT cho thue bao, return luon
						return $codeResult;
					}
				}
			}
		}
		else if($action == self::ACTION_CANCEL) {
			/**
			 * xu ly thue bao thuoc 1 trong nhung truong hop sau:
// 			 * 	 - dang dung dich vu & chua het han -> set pending
			 * 	 - dang dung dich vu & het han -> disable
// 			 *   - dang extend_pending -> disable
			 */
			//neu $ssm != null : do subscriberCommand->scanPendingSub goi de huy nhung thue bao o trang thai extend_pending & expired
			if(($usingServices == null) && ($ssm != null)){
				$usingServices = $ssm;
			}
			if($usingServices != null ){
				$service = Service::model()->findByPk($usingServices->service_id);
				Yii::log(strtolower($service->code_name));
				if($service->code_name == $service->code_name) { //kiem tra huy dung dich vu dang dung ko
					$chargingResult = $subscriber->cancelService($usingServices, $channel_type, -1, "", $user_name, $user_ip);
					if($chargingResult == CPS_OK) {
						$mtMessage = "Quy Khach da huy thanh cong dich vu vFilm. De dang ky lai dich vu, Quy Khach vui long soan DK gui 1579. Cam on Quy Khach da su dung dich vu cua VinaPhone.";
					}
					else {
						if($channel_type != CHANNEL_TYPE_SMS) {
							$codeResult = self::PROCESS_ERROR_GENERAL;
						}
						$mtMessage = "Yeu cau cua Quy Khach chua duoc thuc hien do he thong dang ban. Xin Quy Khach vui long dang ky lai sau. Tran trong cam on.";
					}
				}
				else { //yeu cau huy sai ten goi dang dung
					if($channel_type != CHANNEL_TYPE_SMS) {
						$codeResult = self::PROCESS_ERROR_GENERAL;
					}
					$mtMessage = "Quy khach yeu cau huy sai ten goi dang su dung. Soan \"KT\" (100d) de biet thong tin ve goi cuoc dang su dung.";
				}
			}
			else { //chua dang ky goi dich vu nao. Thong bao loi.
				if($channel_type != CHANNEL_TYPE_SMS) {
					$codeResult = self::PROCESS_ERROR_NOT_USING_SERVICE;
				}
				$mtMessage = "Quy Khach chua dang ky dich vu vFilm cua VinaPhone. De dang ky dich vu, Quy Khach vui long soan DK gui 1579. De biet them chi tiet, Quy Khach vui long lien he tong dai 9191.";
			}
			if($codeResult > 0) { //neu codeResult > -1 ko gui MT cho thue bao, return luon
				return $codeResult;
			}
		}
		else if($action == self::ACTION_EXTEND_SERVICE) {
			$mtMessage = "";
			$recurTransaction = $subscriber->newTransaction($channel_type, USING_TYPE_REGISTER, SubscriberTransaction::PURCHASE_TYPE_EXTEND, $service, null, null , $user_name, $user_ip);
			if($subscriber->status == Subscriber::STATUS_WHITE_LIST) { //xu ly truong hop white_list
				$chargingResult = CPS_OK;
			}
			else {
				$chargingResult = ChargingProxy::chargingExtendService($msisdn, $service, $recurTransaction->id, $channel_type);
			}
			$expireTime = strtotime($ssm->expiry_date);
			echo 'Subscriber id: '.$subscriber->id." | Chargin result: ".$chargingResult."\n";
			if($ssm->recur_retry_times > 0) { //>0 la truy thu, ==0 la gia han
				$recurTransaction->purchase_type = 10;
			}
			if ($chargingResult == CPS_OK) {
				$exp_ts = $expireTime > time() ? $expireTime : time();
				$new_ts = ($exp_ts + ($service->using_days - 1) * 24 * 60 * 60);
				$newexp = new DateTime("@$new_ts");
				//hiendv: chi duoc su dung dv den het ngay (using_days - 1) (d-m-Y 23:59:59) cua chu ky
				$ssm->expiry_date = date('Y-m-d 23:59:59', $new_ts);
				$ssm->modify_date  = new CDbExpression("NOW()");
				$ssm->recur_retry_times = 0;
				//Disable send sms when extend success!
				$needMT = false;
				$ssm->sent_notification = 0;
				$ssm->save(false);
				foreach ($ssm->getErrors() as $k => $v) {
					echo "ssm error $k=$v \n";
				}
				$recurTransaction->status = 1;
				$recurTransaction->error_code = CPS_OK;
				if(!$recurTransaction->update()) {
					print_r($recurTransaction->getErrors());
				}
				if($needMT) {
					$mtMessage = "Dich vu vFilm cua Quy Khach da duoc gia han thanh cong, thoi han su dung den het ngay ".$newexp->format("d/m/Y").". Cam on Quy Khach da su dung dich vu cua VinaPhone.";
				}
			} else { //thanh toan bi loi, luu transaction o trang thai bi loi
// 				if($chargingResult == NOK_NO_MORE_CREDIT_AVAILABLE) {
					$ssm->modify_date  = new CDbExpression("NOW()");
					$ssm->recur_retry_times++;
// 				}
				if($ssm->recur_retry_times == 0) {
					$ssm->recur_retry_times++;
				}
				
				$pendingDuration = 35;
                $now = new DateTime(date('Y-m-d H:i:s'));
                $expiry_date = new DateTime($ssm->expiry_date);
                $last_date_retry = $expiry_date->modify('+34 day');

				//if($ssm->recur_retry_times > $pendingDuration) {
                if ($last_date_retry < $now){
					$subscriber->cancelService($ssm, CHANNEL_TYPE_MAXRETRY);
					$mtMessage = "Tai khoan cua Quy Khach da khong du de gia han dich vu vFilm va dich vu Quy Khach da bi huy. Quy Khach vui long nap them tien vao tai khoan va soan DK gui 1579 de dang ky lai dich vu. Cam on Quy Khach da su dung dich vu cua VinaPhone.";
				} else if ($chargingResult == 6) {//trungdh bo sung
					try {			
						$subscriber->cancelService($ssm, CHANNEL_TYPE_SUBNOTEXIST);
//						$filelog = '/tmp/trungdh.log';
						$infomation = 'Time:'.date('Y/m/d H:i:s').',';
						$infomation .= "Autorenew.Cancel: Thue bao $msisdn - chargingResult: $chargingResult...";
//						file_put_contents($filelog, $infomation."\r\n", FILE_APPEND | LOCK_EX);
					} catch(Exception $e) {		
						echo 'Exception: ',  $e->getMessage(), "\n";				
					}
					
				} else {
					$ssm->save(false);
				}
				
				$recurTransaction->error_code = $chargingResult;
				$recurTransaction->status = 2;
				if(!$recurTransaction->update()) {
					print_r($recurTransaction->getErrors());
				}
			}
		}

		//TODO: Send SMS MT to subscriber

        if($subscriber->id == 240){
            if (!empty($mtMessage)) {
                //luu MO can delay
                if ($channel_type == CHANNEL_TYPE_SMS) {
                    return $mtMessage;
                } else {
                    $res = Vinaphone::sendSms($msisdn, $mtMessage);
                    if (!empty($delaySms)) {
                        $smsQueue = new SmsQueue();
                        $smsQueue->subscriber_id = $subscriber->id;
                        $smsQueue->content = $delaySms;
                        $smsQueue->create_date = date("Y-m-d H:i:s");
                        $expire_delay = Yii::app()->params['expire_sms'];
                        $expire_date = strtotime($smsQueue->create_date) + $expire_delay;
                        $smsQueue->sending_time = date("Y-m-d H:i:s", $expire_date);
                        $smsQueue->retry_time_sms = Yii::app()->params['retry_times_sms'];
                        $smsQueue->save();
                    }
                }
            }
        } else {
            if(!empty($mtMessage)){
                if($channel_type == CHANNEL_TYPE_SMS) { //dang ky || huy bang SMS thi return message content de sendSms ben MolistenerController
                    return $mtMessage;
                }
                $res = Vinaphone::sendSms($msisdn, $mtMessage);
            }
        }
        return self::PROCESS_ERROR_NONE;

    }

    public static function syncSubscriber($channel_type, $action = NULL, $service, $subscriber, $ssm = null, $user_name=NULL, $user_ip=NULL){
        $usingServices = $subscriber != null ?
            ServiceSubscriberMapping::model()->findByAttributes(array('subscriber_id' => $subscriber->id, 'is_active' => 1)):null;
        if(($usingServices == null) && ($ssm != null)){
            $usingServices = $ssm;
        }
        $chargingResult = $subscriber->cancelService($usingServices, $channel_type, -1, "", $user_name, $user_ip);
        if($chargingResult == CPS_OK || $chargingResult == '11' || $chargingResult == '6') {
            $mtMessage = "Hủy dịch vụ và sync thành công!";
        }
        else {
            $mtMessage = "Sync không thành công do hủy dịch vụ thất bại!.";
        }
        return $mtMessage;
    }

    public static function syncSubscriber2($channel_type, $action = NULL, $service, $subscriber, $ssm = null, $user_name=NULL, $user_ip=NULL){
        if (ServiceSubscriberMapping::model()->exists("is_active = 1 and subscriber_id = " . $subscriber->id)){
            $usingServices = ServiceSubscriberMapping::model()->findByAttributes(array('subscriber_id' => $subscriber->id, 'is_active' => 1));
        }
        else{
            $usingServices = new ServiceSubscriberMapping();
            $usingServices->subscriber_id = $subscriber->id;
            $usingServices->service_id = 5;
            $usingServices->activate_date = date('Y-m-d H:i:s');
            $usingServices->expiry_date = date('Y-m-d H:i:s');
            $usingServices->is_active = 1;
            $usingServices->create_date = date('Y-m-d H:i:s');
            $usingServices->save();
        }

        $chargingResult = $subscriber->cancelService($usingServices, $channel_type, -1, "", $user_name, $user_ip);
        if($chargingResult == CPS_OK) {
            $mtMessage = "Hủy dịch vụ và sync thành công!";
        }
        else {
            $mtMessage = "Sync không thành công do hủy dịch vụ thất bại!.";
        }
        return $mtMessage;
    }
	
	public static function giftService($channelType, $subscriber, $receiverNumber, $service){
		/*
		 * subscriber exist
		*/
		if($subscriber == NULL) {
			return self::PROCESS_ERROR_NULL_SUBSCRIBER;
		}

		/*
		 * Check valid phone number
		*/
		$receiverNumber = CUtils::validatorMobile($receiverNumber);
		if($receiverNumber == '') {
			return self::PROCESS_ERROR_INVALID_PHONENUMBER;
		}
		$receiver = Subscriber::newSubscriber($receiverNumber);
		/*
		 * Check exist service
		*/
		if($service == NULL) {
			return self::PROCESS_ERROR_INVALID_SERVICE;
		}

		/*
		 * Check nguoi nhan da su dung goi chua
		*/
		if($receiver->isUsingService()){
			return self::PROCESS_ERROR_USING_SERVICE;
		}

		$giftConfirm = SubscriberGiftConfirmation::newGiftConfirm($subscriber, $receiver, $service);
		if($giftConfirm == NULL) {
			return self::PROCESS_ERROR_GENERAL;
		}

		$content = "Xin chao quy khach, thue bao ".$subscriber->subscriber_number." muon tang quy khach goi ".$service->display_name." cua dich vu xem Phim truc tuyen, gia ".intval($service->price)."d. De xac nhan, quy khach soan CO ".$giftConfirm->confirmation_code." gui 1579 (mien phi) de dong y nhan va xem phim mien phi.";
		$mt = Vinaphone::sendSms($receiver->subscriber_number, $content);
		if($mt->mt_status != 0) {
			return self::PROCESS_ERROR_GENERAL;
		}

		return self::PROCESS_ERROR_NONE;
	}

	public static function confirmGift($subscriber,$usingService, $code){
		/*
		 * Verify code confirm
		*/
		$giftConfirm = SubscriberGiftConfirmation::model()->findByAttributes(array('receiver_id' => $subscriber->id,
				'confirmation_code'=> $code
		));
		if($giftConfirm == NULL){
			$content = "Xin loi, quy khach nhap sai ma so tang, hoac quy khach khong duoc tang goi cuoc nao.";
			Vinaphone::sendSms($subscriber->subscriber_number, $content);
			return self::PROCESS_ERROR_CODE_INVALID;
		}

		$serviceGift = $giftConfirm->service;
		if($serviceGift == NULL){
			$content = "Xin loi, hien tai goi cuoc tang cho ban khong ton tai tren he thong. Xin vui long truy cap http://vfilm.vn hoac goi CSKH de biet them chi tiet";
			Vinaphone::sendSms($subscriber->subscriber_number, $content);
			return self::PROCESS_ERROR_INVALID_SERVICE;
		}

		$sendSubscriber = $giftConfirm->subscriber;
		if($sendSubscriber == NULL){
			$content = "Xin loi, hien nguoi tang goi cuoc cho ban da bi xoa khoi he thong. Xin voi long goi CSKH de biet them chi tiet!";
			Vinaphone::sendSms($subscriber->subscriber_number, $content);
			return self::PROCESS_ERROR_INVALID_SERVICE;
		}

		if(strtotime($giftConfirm->expiration_date) < time()){
			$content = "Yeu cau tang goi ".$serviceGift->display_name." cua quy khach da bi huy do qua thoi han xac nhan. Vui long thu lai hoac truy cap http://vfilm.vn de tiep tuc su dung dich vu.";
			Vinaphone::sendSms($sendSubscriber->subscriber_number, $content);
			$mtMessage = "Xin loi, goi cuoc ".$serviceGift->display_name." do thue bao ".$sendSubscriber->subscriber_number." tang quy khach da het hieu luc. De xem cac bo phim HOT nhat hien nay, truy cap http://vfilm.vn";
			return self::PROCESS_ERROR_GIFT_EXPIRED;
		}
		/*
		 * So sanh xem goi cuoc dang dung co gia tri hon goi cuoc dc tang ko?
		*/
		if(($usingService == NULL) || ($usingService != NULL && $usingService->using_days < $serviceGift->using_days)){
			$mtMessage = "";
			$errorCode = self::PROCESS_ERROR_NONE;
			//Tru tien cua thue bao gui tang
			$subscriberTransaction = $subscriber->newTransaction(CHANNEL_TYPE_SMS, USING_TYPE_SEND_GIFT, SubscriberTransaction::PURCHASE_TYPE_NEW, $serviceGiftnull, null, null , $user_name, $user_ip);
			$chargingResult = ChargingProxy::chargingGift($sendSubscriber->subscriber_number, $serviceGift, 0, intval($subscriberTransaction->cost));
			$isChargingSuccess = false;
			if($chargingResult == CPS_OK) {
				$isChargingSuccess = true;
			}else if($chargingResult == NOK_NO_MORE_CREDIT_AVAILABLE) {
				$errorCode = self::PROCESS_ERROR_NOT_ENOUGH_MONEY;
				$content = "Xin loi, quy khach khong du tien de tang goi ".$serviceGift->display_name." cua dich vu xem Phim truc tuyen. Vui long nap them tien hoac chon goi khac de tang. Goi PHIM7 (15.000d/7 ngay). ";
				Vinaphone::sendSms($sendSubscriber->subscriber_number, $content);
				$mtMessage = "Xin loi. Nguoi tang goi cuoc cho quy khach khong du tien de thuc hien giao dich!";
			}
			else {
				$mtMessage = "Xin loi, yeu cau tang goi cua quy khach khong thanh cong do he thong ban. Vui long thu lai sau hoac truy cap http://vfilm.vn hoac goi CSKH de biet them chi tiet";
				$errorCode = self::PROCESS_ERROR_GENERAL;
			}
			Yii::log("mtMessage : $mtMessage");
			if($isChargingSuccess) {
				$subscriberTransaction->status = 1;
				$subscriberTransaction->error_code = CPS_OK;
				$subscriberTransaction->create_date = new CDbExpression('NOW()');
				$subscriberTransaction->update();
				/*
				 * Add service nguoi nhan
				*/
				//Reset all service maping cua nguoi dung (xoa service mapping o trang thai pending extend, pending huy)
				$pendingService = ServiceSubscriberMapping::model()->findByAttributes(array("subscriber_id" => $subscriber->id, "is_active" => ServiceSubscriberMapping::SERVICE_STATUS_PENDING));
				if($pendingService != NULL){
					$chargingResultCancelPending = self::chargingCancel($subscriber->subscriber_number, $pendingService->service);
					$subscriber->cancelService($pendingService, CHANNEL_TYPE_SMS, null, $chargingResultCancelPending);
				}
				$subscriberService = $subscriber->addService($serviceGift,null,USING_TYPE_RECEIVE_GIFT);
				$content = "Quy khach da tang thanh cong goi ".$serviceGift->display_name." cho thue bao ".$subscriber->subscriber_number.". Tai khoan cua quy khach bi tru ".intval($serviceGift->price)." dong. Cam on quy khach da su dung dich vu Xem phim truc tuyen";
				Vinaphone::sendSms($sendSubscriber->subscriber_number, $content);
				$mtMessage = "Quy khach da nhan duoc goi ".$serviceGift->display_name." cua dich vu xem phim truc tuyen, han su dung den ".date('d/m/Y H:i', strtotime($subscriberService->expiry_date)).". De su dung dich vu, truy cap http://vfilm.vn hoac goi CSKH de biet them chi tiet";
			}
			else {
				$subscriberTransaction->error_code = $chargingResult;
				$subscriberTransaction->create_date = new CDbExpression('NOW()');;
				$subscriberTransaction->status = 2;
				$subscriberTransaction->update();
			}
			if(!empty($mtMessage)){
				Vinaphone::sendSms($subscriber->subscriber_number, $mtMessage);
			}
			return $errorCode;

		}else{
			// 			$content = "Quy khach dang su dung goi ".$usingService->service->display_name." cua dich vu xem phim truc tuyen, han su dung den ".date('d/m/Y H:i', strtotime($usingService->expiry_date)).". De su dung dich vu, truy cap http://vfilm.vn hoac goi CSKH de biet them chi tiet";
			$content = "Quy khach hien dang su dung goi dich vu xem Phim truc tuyen-".$usingService->service->display_name.", ". intval($usingService->service->price) ."d, co thoi han den ".date('d/m/Y H:i', strtotime($usingService->expiry_date)).". Xin cam on!";
			Vinaphone::sendSms($subscriber->subscriber_number, $content);
			return self::PROCESS_ERROR_USING_SERVICE;
		}

	}

	public static function getNameErrorCode($error_code, $channelType = CHANNEL_TYPE_WAP){
		$name = "";
		switch($error_code){
			case self::PROCESS_ERROR_GENERAL:
				if($channelType == CHANNEL_TYPE_SMS){
					$name = "Xay ra loi trong qua trinh giao dich. Xin vui long thu lai sau";
				}else{
					$name = "Xảy ra lỗi trong quá trình giao dịch. Xin vui lòng thử lại sau";
				}
				break;
			case self::PROCESS_ERROR_INVALID_SERVICE:
				if($channelType == CHANNEL_TYPE_SMS){
					$name = "Xin loi, Goi cuoc khong ton tai tren he thong. xin vui long truy cap http://vfilm.vn hoac goi CSKH de biet them chi tiet";
				}else{
					$name = "Gói cước không tồn tại";
				}
				break;
			case self::PROCESS_ERROR_NOT_ENOUGH_MONEY:
				if($channelType == CHANNEL_TYPE_SMS){
					$name = "Xin loi, quy khong khong du tien de thuc hien giao dich. Vui long nap them tien hoac chon goi khac.";
				}else{
					$name = "Tài khoản của quí khách không đủ để thực hiện giao dịch";
				}
				break;
			case self::PROCESS_ERROR_NOT_USING_SERVICE:
				if($channelType == CHANNEL_TYPE_SMS){
					$name = "Hien quy khach dang khong su dung goi cuoc nao cua dich vu vFilm";
				}else{
					$name = "Hiện quý khách đang không sử dụng gói cước nào của dịch vụ vFilm";
				}
				break;
			case self::PROCESS_ERROR_USING_SERVICE:
				if($channelType == CHANNEL_TYPE_SMS){
					$name = "Xin loi, hien quy khach da dang ky su dung goi cuoc cua dich vu vFilm";
				}else{
					$name = "Hiện quý khách đã đăng ký gói cước của dịch vụ vFilm";
				}
				break;
			case self::PROCESS_ERROR_INVALID_PHONENUMBER:
				if($channelType == CHANNEL_TYPE_SMS){
					$name = "Xin loi, quy khach vua nhap sai so dien thoai nhan. Vui long kiem tra va thu lai. Xin cam on !";
				}else{
					$name = "Số điện thoại không hợp lệ";
				}
				break;
			case self::PROCESS_ERROR_NULL_SUBSCRIBER:
				if($channelType == CHANNEL_TYPE_SMS){
					$name = " Xin loi, yeu cau cua quy khach khong thanh cong do he thong ban. Vui long thu lai sau hoac truy cap http://vfilm.vn hoac goi CSKH de biet them chi tiet";
				}else{
					$name = "Số điện thoại không hợp lệ";
				}
				break;
			case self::PROCESS_ERROR_FREE_VOD: //mua phim le thi ko co channel type la sms
				$name = "Phim này miễn phí, quý khách không cần trả tiền để xem phim này";
				break;
			case self::PROCESS_ERROR_BOUGHT_VOD: //mua phim le thi ko co channel type la sms
				$name = "Phim này đã mua rồi.";
				break;
		}
		return $name;
	}
}

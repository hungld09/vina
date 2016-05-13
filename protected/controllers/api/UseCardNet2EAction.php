<?php
class UseCardNet2EAction extends CAction {
	
	public function run(){
        header('Content-type: application/json');
		$issuer = isset($_POST['issuer']) ? $_POST['issuer'] : null;
		$cardSerial = isset($_POST['cardSerial']) ? $_POST['cardSerial'] : null;
		$cardCode = isset($_POST['cardCode']) ? $_POST['cardCode'] : null;
		$subId = isset($_POST['subId']) ? $_POST['subId'] : null;
		Yii::log("-------------------------------------------------------------------------");
		//check param co gia tri hay khong
		if($cardSerial == null){
			ContentResponse::getErrorMessage(PARAM_INVALID, "cardSerial");
			return;
		}
		if($cardCode == null){
			ContentResponse::getErrorMessage(PARAM_INVALID, "cardCode");
			return;
		}
		if($issuer == null){
			ContentResponse::getErrorMessage(PARAM_INVALID, "issuer");
			return;
		}
		if($subId == null){
			ContentResponse::getErrorMessage(PARAM_INVALID, "subId");
			return;
		}
//                echo json_encode(array('cardSerial' => $cardSerial, 'cardCode' => $cardCode));return;
		//get subscriber -- ktra co trong he thong chua
		$subscriber = Subscriber::model()->findByPk($subId);
		if($subscriber == null){
			ContentResponse::getErrorMessage(SUB_NOT_EXIST, "subId");
			return;
		}
                if($subscriber->partner_id == null){
                    $partner_id = 'net2e';
                }else{
                    $partner_id = $subscriber->partner_id;
                }
//                $cardSerial = base64_decode($cardSerial);
//                $cardCode = base64_decode($cardCode);
		//ktra xem seria + cardcode da dung dinh dang chua
		if(!$this->checkCardCode($issuer, $cardCode, $cardSerial)) return;
		
		//check nap sai qua 3 lan -> block 5phut
		$transaction = $subscriber->newTransaction(PURCHASE_TYPE_NEW, $cardCode, $cardSerial, $issuer, $partner_id);
		
		//Nap the cua don vi phat hanh Hocde
                if(strtoupper($issuer)  === HOCDE){
			$resultoObj = $this->useCardHocde($cardSerial, $cardCode, $subscriber->id);
		} else if(strtoupper($issuer)  === ONCASH){// don vi phat hanh VDC
                    $net2EPayment = new Net2EPayment();
                    $res = $net2EPayment->paymentNet2E($issuer, $cardSerial, $cardCode, $transaction->id, $subscriber->username);
                    $resultoObj = ContentResponse::parserResultNet2e($res);
		} else { //Nap the cua cac don vi mobile
                    // kenh net2E
                    $net2EPayment = new Net2EPayment();
                    $res = $net2EPayment->paymentNet2E($issuer, $cardSerial, $cardCode, $transaction->id, $subscriber->username);
                    $resultoObj = ContentResponse::parserResultNet2e($res);
		}
                //hocde
                if(strtoupper($issuer)  === HOCDE){
                    if($resultoObj->errorCode == CARD_OK){
                            $transaction->status = 1;
                            $subscriber->fcoin += intval($resultoObj->amount)/100;
                            $subscriber->save();
                        }else{
                            ContentResponse::getErrorMessage($resultoObj->errorCode); return;
                        }
                        $transaction->error_code = $resultoObj->errorCode;
                        $transaction->description = $resultoObj->errorMessage;
                        $transaction->cost = $resultoObj->amount;
                        $transaction->oncash = (intval($resultoObj->amount))/100;
                        $transaction->save();
                        echo json_encode(array('code' => $resultoObj->errorCode, 'message' => $resultoObj->errorMessage, 'fcoin'=> $subscriber->fcoin, 'amount'=>$resultoObj->amount));
                }else{
                    Yii::log("\n Ma code nap the: " . $resultoObj->returnCode);
                    if($resultoObj->returnCode == CARD_ONCASH_OK){
//                        $this->checkLoopUseCard($subscriber->id, false);
                        $transaction->status = 1;
                        $subscriber->fcoin += intval($resultoObj->cardPrice)/100;
                        $subscriber->save();
                    } else {
                        Yii::log("\n Ma code nap the: " . $resultoObj->returnCode);
//                            if(!$this->checkLoopUseCard($subscriber->id, true)){
//                                    ContentResponse::getErrorMessageNet2E(CARD_LOOP_INVALID, "");
//                                    return;
//                            }
                            ContentResponse::getErrorMessageNet2E($resultoObj->returnCode); return;
                    }
                    $transaction->error_code = $resultoObj->returnCode;
                    $transaction->card_seria = $resultoObj->returnSerial;
                    $transaction->description = $resultoObj->returnDescription;
                    $transaction->cost = $resultoObj->cardPrice;
                    $transaction->oncash = (intval($resultoObj->cardPrice))/100;
                    $transaction->save();
                    echo json_encode(array('code' => $resultoObj->returnCode, 'message' => 'Bạn đã nạp tiền thành công.', 'fcoin'=> $subscriber->fcoin, 'amount'=>$resultoObj->cardPrice));
                }  
	}
	
	public function checkCardCode($issuer, $cardCode, $cardSerial){
		switch (strtoupper($issuer)){
			case VIETTELCARD:
				if(strlen($cardSerial) > 15 || strlen($cardSerial) < 11){
					ContentResponse::getErrorMessage(CARD_SERIA_INVALID, "cardSerial");
					return false;
				}
				if(strlen($cardCode) > 15 || strlen($cardCode) < 13 || !preg_match('/^[0-9]*$/', $cardCode)){
					ContentResponse::getErrorMessage(CARD_SERIA_CODE_INVALID, "cardCode");
					return false;
				}
				break;
			case MOBICARD:
				if((strlen($cardCode) != 12 && strlen($cardCode) != 14 && strlen($cardCode) != 15) || !preg_match('/^[0-9]*$/', $cardCode)){
					ContentResponse::getErrorMessage(CARD_SERIA_CODE_INVALID, "cardCode");
					return false;
				}
				break;
			case VINACARD:
				if((strlen($cardCode) != 12 && strlen($cardCode) != 14) || !preg_match('/^[0-9]*$/', $cardCode)){
					ContentResponse::getErrorMessage(CARD_SERIA_CODE_INVALID, "cardCode");
					return false;
				}
				break;
			case HOCDE:
				if((strlen($cardCode) != 14 && strlen($cardCode) != 15)){
					ContentResponse::getErrorMessage(CARD_SERIA_CODE_INVALID, "cardCode");
					return false;
				}
				if(strlen($cardSerial) != 14 && strlen($cardSerial) != 15){
					ContentResponse::getErrorMessage(CARD_SERIA_INVALID, "cardSerial");
					return false;
				}
				break;
		}
		return true;
	}
	
	public function checkLoopUseCard($subscriber_id, $error = true){
		$today = date("Y-m-d");
		$blocktime = 360; //s
		$str_today = strtotime($today);
		Yii::log("Check Loop Use Card today: $str_today\n");
		if ($error){
			if(CUtils::hasCookie($subscriber_id)){
				$value = explode("|",CUtils::getCookie($subscriber_id));
				Yii::log("cookie str_today:\n");
				if($str_today == $value[1]){
					$time_use = intval($value[0]) + 1; 
					$cookieTmp = $time_use."|".$value[1];
					CUtils::setCookie($subscriber_id, $cookieTmp, $blocktime);
					if($time_use > 3) return false;
				} else {
					Yii::log("Check Loop Use Card remove cooke:\n");
					CUtils::removeCookie($subscriber_id);
					return true;
				}
			} else {
				Yii::log("Check Loop Use Card set cookie sub_id: $subscriber_id | str_today: $str_today| block: $blocktime\n");
				CUtils::setCookie($subscriber_id, "1|$str_today", $blocktime);
				return true;
			}
		} else {
			Yii::log("Check Loop Use Card remove cooke:\n");
			CUtils::removeCookie($subscriber_id);
			return true;
		}
	}
	
	protected function useCardHocde($cardSeria, $cardCode,  $subscriberId){
		$checkCard = GenerateCard::model()->findByAttributes(array('card_seria' => $cardSeria, 'status'=>1));
		$resultoObj = ContentResponse::getResultCode();
		if($checkCard != null){
                    if($checkCard->type == 1){
			if($checkCard->card_code === $cardCode){
				$checkCard->status = 2;
				if($checkCard->save()){
					$resultoObj->amount = $checkCard->amount;
					$resultoObj->errorCode = CARD_OK;
					$resultoObj->errorMessage = 'Kiem tra thanh cong';
				} else {
					$resultoObj->errorCode = CARD_NOT_OK;
					$resultoObj->errorMessage = 'Loi he thong! xin vui long nap lai!';
				}
			}
                    }else{
                        Yii::log("\n partner the: " . $checkCard->partner);
//                        if($checkCard->partner == 'net2e'){
//                            $resultoObj = $this->checkPartnerNet2e($checkCard, $cardCode, $resultoObj, $subscriberId);
//                        }else 
                        if($checkCard->partner == 'danglai'){
                            $resultoObj = $this->checkPartnerDanglai($checkCard, $cardCode, $resultoObj, $subscriberId);
                        }else {
                            if($checkCard->card_code === $cardCode){
				$checkCard->status = 2;
				if($checkCard->save()){
					$resultoObj->amount = $checkCard->amount;
					$resultoObj->errorCode = CARD_OK;
					$resultoObj->errorMessage = 'Kiem tra thanh cong';
				} else {
					$resultoObj->errorCode = CARD_NOT_OK;
					$resultoObj->errorMessage = 'Loi he thong! xin vui long nap lai!';
				}
                            }
			}
                    }
		} else {
			$resultoObj->errorCode = CARD_NOT_OK;
			$resultoObj->errorMessage = 'Ma so nap tien khong ton tai hoac da duoc su dung';
		}
		return $resultoObj;
	}
        static function decrypt($value, $key){
            $value = str_replace(' ', '+', $value);
            $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
            $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256,  pack("H*", $key), base64_decode($decrypt), MCRYPT_MODE_ECB, $iv);
            return $decrypted;
        }
        public function checkPartnerNet2e($checkCard, $cardCode, $resultoObj, $subscriberId){
        if($checkCard->partner == 'net2e'){
            $checkPromition = CheckPromotion::model()->findAllByAttributes(array('subscriber_id'=>$subscriberId, 'km1'=>1));
            $km = 1;
        }
        Yii::log("\n km nap the: " . $km);
        Yii::log("\n subsId nap the: " . $subscriberId);
//        $checkPromition = CheckPromotion::model()->findAllByAttributes(array('subscriber_id'=>Yii::app()->session['user_id'], 'km1'=>1));
        if($checkCard->card_code === $cardCode){
            if(count($checkPromition) == 0){
                $checkCard->status = 2;
                $checkPromition= new CheckPromotion;
                $checkPromition->subscriber_id = $subscriberId;
                $checkPromition->km1 = $km; //1: net2e, 2:danglai
                $checkPromition->created_date = date('Y-m-d H:i:s');
                $checkPromition->save();
                    if($checkCard->save()){
                            $resultoObj->amount = $checkCard->amount;
                            $resultoObj->errorCode = CARD_OK;
                            $resultoObj->errorMessage = 'Kiem tra thanh cong';
                    } else {
                            $resultoObj->errorCode = CARD_NOT_OK;
                            $resultoObj->errorMessage = 'Loi he thong! xin vui long nap lai!';
                    }
            }else{
                $resultoObj->errorCode = INACTIVECARD;
                $resultoObj->errorMessage = 'Moi tai khoan chi duoc nap toi da 1 the khuyen mai!';
            }
        }else{
            $resultoObj->errorCode = CARD_NOT_OK;
            $resultoObj->errorMessage = 'Loi he thong! xin vui long nap lai!';
        }
        return $resultoObj;
    }
    public function checkPartnerDanglai($checkCard, $cardCode, $resultoObj, $subscriberId){
        $checkPromition = CheckPromotion::model()->findAllByAttributes(array('subscriber_id'=>$subscriberId, 'km1'=>2));
        if($checkCard->card_code === $cardCode){
            if(count($checkPromition) < 2){
                $checkCard->status = 2;
                $checkPromition= new CheckPromotion;
                $checkPromition->subscriber_id = $subscriberId;
                $checkPromition->km1 = 2; //1: net2e, 2:danglai
                $checkPromition->save();
                    if($checkCard->save()){
                            $resultoObj->amount = $checkCard->amount;
                            $resultoObj->errorCode = CARD_OK;
                            $resultoObj->errorMessage = 'Kiem tra thanh cong';
                    } else {
                            $resultoObj->errorCode = CARD_NOT_OK;
                            $resultoObj->errorMessage = 'Loi he thong! xin vui long nap lai!';
                    }
            }else{
                $resultoObj->errorCode = INACTIVECARD;
                $resultoObj->errorMessage = 'Moi tai khoan chi duoc nap toi da 2 the khuyen mai!';
            }
        }else{
            $resultoObj->errorCode = CARD_NOT_OK;
            $resultoObj->errorMessage = 'Loi he thong! xin vui long nap lai!';
        }
        return $resultoObj;
    }
}
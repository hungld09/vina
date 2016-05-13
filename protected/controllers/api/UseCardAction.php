<?php
class UseCardAction extends CAction {
	
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
		//get subscriber -- ktra co trong he thong chua
		$subscriber = Subscriber::model()->findByPk($subId);
		if($subscriber == null){
			ContentResponse::getErrorMessage(SUB_NOT_EXIST, "subId");
			return;
		}
		//ktra xem seria + cardcode da dung dinh dang chua
		if(!$this->checkCardCode($issuer, $cardCode, $cardSerial)) return;
		
		//check nap sai qua 3 lan -> block 5phut
		$transaction = $subscriber->newTransaction(PURCHASE_TYPE_NEW, $cardCode, $cardSerial, $issuer);
		
		//Nap the cua don vi phat hanh hocde
		if(strtoupper($issuer)  === HOCDE){
			$resultoObj = $this->useCardHocde($cardSerial, $cardCode);
		} else { //Nap the cua cac don vi mobile
			$voucherPayment = new VoucherPayment();
			$res = $voucherPayment->payment($issuer, $cardSerial, $cardCode, $transaction->id);
			$resultoObj = ContentResponse::parserResult($res);
		}
		if($resultoObj->errorCode == CARD_OK){
			$this->checkLoopUseCard($subscriber->id, false);
			$transaction->status = 1;
			$subscriber->fcoin += intval($resultoObj->amount)/1000;
			$subscriber->save();
		} else {
			if(!$this->checkLoopUseCard($subscriber->id, true)){
				ContentResponse::getErrorMessage(CARD_LOOP_INVALID, "");
				return;
			}
		}
		$transaction->error_code = $resultoObj->errorCode;
		$transaction->description = $resultoObj->errorMessage;
		$transaction->cost = $resultoObj->amount;

		$transaction->save();
		echo json_encode(array('code' => $resultoObj->errorCode, 'message' => $resultoObj->errorMessage, 'fcoin'=> $subscriber->fcoin, 'amount'=>$resultoObj->amount));
	}
	
	public function checkCardCode($issuer, $cardCode, $cardSerial){
		switch (strtoupper($issuer)){
			case VT:
				if(strlen($cardSerial) > 15 || strlen($cardSerial) < 11){
					ContentResponse::getErrorMessage(CARD_SERIA_INVALID, "cardSerial");
					return false;
				}
				if(strlen($cardCode) > 15 || strlen($cardCode) < 13 || !preg_match('/^[0-9]*$/', $cardCode)){
					ContentResponse::getErrorMessage(CARD_SERIA_CODE_INVALID, "cardCode");
					return false;
				}
				break;
			case MOBI:
				if((strlen($cardCode) != 12 && strlen($cardCode) != 14 && strlen($cardCode) != 15) || !preg_match('/^[0-9]*$/', $cardCode)){
					ContentResponse::getErrorMessage(CARD_SERIA_CODE_INVALID, "cardCode");
					return false;
				}
				break;
			case VINA:
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
	
	protected function useCardHocde($cardSeria, $cardCode){
		$checkCard = GenerateCard::model()->findByAttributes(array('card_seria' => $cardSeria, 'status'=>1));
		$resultoObj = ContentResponse::getResultCode();
		if($checkCard != null){
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
		} else {
			$resultoObj->errorCode = CARD_NOT_OK;
			$resultoObj->errorMessage = 'Ma so nap tien khong ton tai hoac da duoc su dung';
		}
		return $resultoObj;
	}
}
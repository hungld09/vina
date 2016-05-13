<?php
class Net2EPayment {
	public $partnerCode;
	public $password;
	public $username;
	public $partner_id;
	
	function __construct(){
		$this->getConfig();
	}
	
	public function getConfig(){
		$cfg = Yii::app()->params['net2e'];
		$this->username = $cfg['username'];
		$this->partnerCode = $cfg['partnerCode'];
		$this->password = $cfg['password'];
		$this->partner_id = $cfg['partner_id'];
	}

	/*
	 * Func nap the param: (ONCASH), seri the, ma the, ma giao dich
	 */
	public function paymentNet2E($issuer, $cardSerial, $cardCode, $transId, $username){
		$net2e = new Net2E();
		$topupPpCardForPartners = new TopupPpCardForPartners();
		$topupPpCardForPartners->partnerCode = $this->partnerCode;
		$topupPpCardForPartners->cardName = $issuer;
		$topupPpCardForPartners->transID = $transId;
		$topupPpCardForPartners->userName = $username;
		$topupPpCardForPartners->topupAmount = 0;
		$topupPpCardForPartners->cardSerial = $cardSerial;
		$topupPpCardForPartners->PinCodeBase64 = base64_encode($cardCode);
		$topupPpCardForPartners->customerIP = $this->partner_id;
		$topupPpCardForPartners->description = '';
		$topupPpCardForPartners->signature = $this->generateSignature($transId, $cardCode, $username);
		$topupPpCardForPartners->language = 'vi-VN';
		$topupPpCardForPartners->cardPrice = '0';
		$topupPpCardForPartners->cardOnCash = '0';
		$topupPpCardForPartners->returnCode = '0';
		Yii::log("issuer: $issuer | username: $this->username | cardCode:  $cardCode | cardSerial: $cardSerial | cardCode: $cardCode | transId: $transId 
		| partnerCode: $this->partnerCode | PinCodeBase64: $topupPpCardForPartners->PinCodeBase64| password: $this->password | username: $username | signature: $topupPpCardForPartners->signature\n");
		$useCardResponse = $net2e->TopupPpCardForPartners($topupPpCardForPartners);
//		Yii::log("net2e useCard response: ".$useCardResponse);
//                echo '<pre>'; print_r($useCardResponse);die;
		return $useCardResponse;
	}
	public function generateSignature($transId, $pinCode, $username){
		$userName = $this->username;
		$password = $this->password;
		$partnerCode = $this->partnerCode;
		$customerIP = $this->partner_id;
		$transId = $transId;
		//$pinCode = MD5(strtoupper($pinCode));
		$pinCode = strtoupper(MD5($pinCode));
		Yii::log("pinCode UP: ".$pinCode);
		return md5($userName.$password.$partnerCode.$customerIP.$username.$transId.$pinCode.'pinOol');
	}
	
}

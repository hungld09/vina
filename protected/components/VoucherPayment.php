<?php
class VoucherPayment {
	public $partnerCode;
	public $password;
	public $secretKey;
	
	function __construct(){
		$this->getConfig();
	}
	
	public function getConfig(){
		$cfg = Yii::app()->params['voucher'];
		$this->partnerCode = $cfg['partnerCode'];
		$this->password = $cfg['password'];
		$this->secretKey = $cfg['secretKey'];
	}

	/*
	 * Func nap the param: (VT|MOBI), seri the, ma the, ma giao dich
	 */
	public function payment($issuer, $cardSerial, $cardCode, $transId){
		$voucher = new Voucher();
		$useCard = new useCard();
		$useCard->issuer = $issuer;
		$useCard->cardSerial = $cardSerial;
		$useCard->cardCode = $cardCode;
		$useCard->transRef = $transId; //ma giao dich
		$useCard->partnerCode = $this->partnerCode;
		$useCard->password = $this->password;
		$signature = $this->generateSignature($issuer, $cardCode, $transId);
		$useCard->signature = $signature;
		Yii::log("issuer: $issuer | cardSerial: $cardSerial | cardCode: $cardCode | transId: $transId 
		| partnerCode: $this->partnerCode | password: $this->password | signature: $signature\n");
		$useCardResponse = $voucher->useCard($useCard);
		Yii::log("Voucher useCard response: ".$useCardResponse->return);
		return $useCardResponse->return;
	}
	
	/*
	 * Func lay thong tin chi tiet giao dich
	 * param: ma giao dich can lay
	 */
	public function getPaymentDetail($transId){
		$voucher = new Voucher();
		$getTransactionDetail = new getTransactionDetail();
		$getTransactionDetail->transRef = $transId; //ma giao dich
		$getTransactionDetail->partnerCode = $this->partnerCode;
		$getTransactionDetail->password = $this->password;
		$signature = $this->generateSignatureTrans($transId);
		$getTransactionDetail->signature = $signature;
		Yii::log("getPaymentDetail transId: $transId | partnerCode: $this->partnerCode | password: $this->password | signature: $signature\n");
		$getTransactionDetailResponse = $voucher->getTransactionDetail($getTransactionDetail);
		Yii::log("Voucher getTransactionDetail response: ".$getTransactionDetailResponse->return);
		return $getTransactionDetailResponse->return;
	}
	
	public function generateSignature($issuer, $cardCode, $transId){
		$secretKey = $this->secretKey;
		$partnerCode = $this->partnerCode;
		$password = $this->password;
		return MD5($issuer.$cardCode.$transId.$partnerCode.$password.$secretKey);
	}
	
	public function generateSignatureTrans($transId){
		$secretKey = $this->secretKey;
		$partnerCode = $this->partnerCode;
		$password = $this->password;
		return MD5($transId.$partnerCode.$password.$secretKey);
	}
	
}
<?php
class TransactionDetailAction extends CAction {
	public function run(){
            header('Content-type: application/json');
		$issuer = isset($_POST['transId']) ? $_POST['transId'] : null;
		$voucherPayment = new VoucherPayment();
		$res = $voucherPayment->getPaymentDetail($transId);
		echo json_encode(array('code' => 5, 'message' => $res));
	}
}
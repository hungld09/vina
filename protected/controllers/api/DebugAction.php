<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class DebugAction extends CAction{
//    public function run(){
//        header('Content-type: application/json');
//    	$sessionKey = isset($_POST['sessionkey']) ? $_POST['sessionkey'] : null;
//    	if($sessionKey == null){
//    		$sessionKey = isset($_GET['sessionkey']) ? $_GET['sessionkey'] : null;
//    	}
//    	echo time();
////        $sessionKey = CUtils::generateSessionKey(1);
//        $sessionKey = str_replace(' ','+',$sessionKey);
//        echo "\nDecrypt: ".CUtils::decrypt($sessionKey, secret_key);
//        if(CUtils::checkAuthSessionKey($sessionKey)){
//        	echo "\nsessionKey ok!";
//        }
//    }
	public function run(){
		/*
		$numberCard = 10;
		$amount = 10000;
		$status = 1;
		$sqlQuery = "insert into generate_card(card_seria, card_code, status, create_date, amount) values ";
		for($i = 1; $i <= $numberCard; $i++){
			$card_seria = microtime(true)*10000+$i;
			$time = date("Y-m-d H:i:s");
			$card_code = strtoupper(substr(sha1($card_seria.$i), rand(0, 20), 14));
//			echo sha1($card_seria.$i)."\n";
			if($i == $numberCard) {
				$sqlQuery .= "('$card_seria','$card_code',$status,'$time',$amount);";
			} else {
				$sqlQuery .= "('$card_seria','$card_code',$status,'$time',$amount),";
			}
		}
//		echo $sqlQuery;
		Yii::app()->db->createCommand($sqlQuery)->query();
		*/
		
		$number_user = 100;
		$status = 1;
		$sqlQuery = "insert into subscriber (username, password, status, type, create_date) values ";
		$password = "e10adc3949ba59abbe56e057f20f883e_echat";
		$type = 1;
		for($i = 1; $i <= $number_user; $i++){
			$time = date("Y-m-d H:i:s");
			$username = "thay_".$i;
			if($i == $number_user) {
				$sqlQuery .= "('$username','$password',$status,$type,'$time');";
			} else {
				$sqlQuery .= "('$username','$password',$status,$type,'$time'),";
			}
		}
		Yii::app()->db->createCommand($sqlQuery)->query();
		
		$sqlQuery = "insert into subscriber (username, password, status, type, create_date) values ";
		$password = "e10adc3949ba59abbe56e057f20f883e_echat";
		$type = 1;
		for($i = 1; $i <= $number_user; $i++){
			$time = date("Y-m-d H:i:s");
			$username = "hocsinh_".$i;
			if($i == $number_user) {
				$sqlQuery .= "('$username','$password',$status,$type,'$time');";
			} else {
				$sqlQuery .= "('$username','$password',$status,$type,'$time'),";
			}
		}
		Yii::app()->db->createCommand($sqlQuery)->query();
	}
}

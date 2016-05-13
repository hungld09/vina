<?php
class ResultCode{
	public $errorCode;
	public $errorMessage;
	public $amount = 0;
}
class ResultCodeNet2E{
	public $cardPrice;
	public $returnSerial;
	public $cardOnCash;
	public $returnCode;
	public $returnDescription;
}

class ContentResponse {
	
	public static function getErrorMessage($case=PARAM_INVALID, $param=null){
		switch ($case){
			case PARAM_INVALID:
				$message =  "Param ".$param." Invalid!";
				echo json_encode(array('code' => PARAM_INVALID, 'message' => $message));
				break;
			case SUB_NOT_EXIST:
				$message =  "Subscriber not exist!";
				echo json_encode(array('code' => SUB_NOT_EXIST, 'message' => $message));
				break;
			case CARD_SERIA_INVALID:
				$message =  "Serial thẻ không hợp lệ!";
				echo json_encode(array('code' => CARD_SERIA_INVALID, 'message' => $message));
				break;
			case CARD_SERIA_CODE_INVALID:
				$message =  "Mã thẻ không hợp lệ!";
				echo json_encode(array('code' => CARD_SERIA_CODE_INVALID, 'message' => $message));
				break;
			case CARD_LOOP_INVALID:
				$message =  "Gach qua 3 lan";
				echo json_encode(array('code' => CARD_LOOP_INVALID, 'message' => $message));
				break;
			case SESSION_KEY_INVALID:
				$message =  "sessionkey invalid";
				echo json_encode(array('code' => SESSION_KEY_INVALID, 'message' => $message));
				break;
                        case CARD_NOT_OK:
                            $message =  "Bạn vui lòng kiểm tra lại mã code, Lưu ý phân biệt chữ hoa chữ thường";
                             echo json_encode(array('code' => CARD_NOT_OK, 'message' => $message));
				break;
                       case INACTIVECARD:
                            $message =  "Mỗi tài khoản chỉ được nạp tối đa 1 thẻ khuyến mại.";
                        echo json_encode(array('code' => INACTIVECARD, 'message' => $message));
				break;
		}
		return;
	}
	
	public static function getErrorMessageWap($case=PARAM_INVALID, $param=null){
		switch ($case){
			case PARAM_INVALID:
				$message =  "Param ".$param." Invalid!";
				break;
			case SUB_NOT_EXIST:
				$message =  "Subscriber not exist!";
				break;
			case CARD_SERIA_INVALID:
				$message =  "Serial thẻ không hợp lệ!";
				break;
			case CARD_SERIA_CODE_INVALID:
				$message =  "Mã thẻ không hợp lệ!";
				break;
			case CARD_LOOP_INVALID:
				$message =  "Gạch thẻ quá 3 lần";
				break;
			case SESSION_KEY_INVALID:
				$message =  "Tài khoản của bạn đang được sử dụng!";
				break;
			case CARD_NOT_OK:
				$message =  "Bạn vui lòng kiểm tra lại mã code, Lưu ý phân biệt chữ hoa chữ thường";
				break;
			case INACTIVECARD:
				$message =  "Mỗi tài khoản chỉ được nạp tối đa 1 thẻ khuyến mại.";
				break;
                        default: $message =  "Thông tin thẻ không đúng hoặc không hợp lệ, bạn vui lòng kiểm tra lại";
		}
		return $message;
	}
	public static function getErrorMessageNet2E($case=CARD_ONCASH_CONNECT_INVALID, $param=null){
		switch ($case){
			case CARD_ONCASH_CONNECT_INVALID:
				$message =  "Thông tin thẻ không đúng hoặc không hợp lệ, bạn vui lòng kiểm tra lại";
                                echo json_encode(array('code' => CARD_ONCASH_CONNECT_INVALID, 'message' => $message));
				break;
			case CARD_ONCASH_CODE_FORMAT_INVALID:
				$message =  "Thẻ không hợp lệ";
                                echo json_encode(array('code' => CARD_ONCASH_CODE_FORMAT_INVALID, 'message' => $message));
				break;
			case CARD_ONCASH_TRANS_NOTEXIST:
				$message =  "Mã thẻ không tồn tại";
                                echo json_encode(array('code' => CARD_ONCASH_TRANS_NOTEXIST, 'message' => $message));
				break;
			case CARD_ONCASH_DELETE:
				$message =  "Thẻ đã bị xóa";
                                echo json_encode(array('code' => CARD_ONCASH_DELETE, 'message' => $message));
				break;
			case CARD_ONCASH_EXPIRY:
				$message =  "Thẻ hết hạn sử dụng";
                                echo json_encode(array('code' => CARD_ONCASH_EXPIRY, 'message' => $message));
				break;
			case CARD_ONCASH_USE:
				$message =  "Thẻ đã sử dụng";
                                echo json_encode(array('code' => CARD_ONCASH_USE, 'message' => $message));
				break;
			case CARD_ONCASH_NOT_OK:
				$message =  "Giao dịch không thành công";
                                echo json_encode(array('code' => CARD_ONCASH_NOT_OK, 'message' => $message));
				break;
                        case CARD_LOOP_INVALID:
                            $message =  "Gach qua 3 lan";
                            echo json_encode(array('code' => CARD_LOOP_INVALID, 'message' => $message));
                            break;
                        default: $message =  "Giao dịch bị khóa do nạp quá 5 lần sai";
                            echo json_encode(array('code' => 251, 'message' => $message));
		}
		return $message;
	}
	public static function getErrorMessageWapNet2E($case=CARD_ONCASH_CONNECT_INVALID, $param=null){
		switch ($case){
			case CARD_ONCASH_CONNECT_INVALID:
				$message =  "Thông tin thẻ không đúng hoặc không hợp lệ, bạn vui lòng kiểm tra lại";
				break;
			case CARD_ONCASH_CODE_FORMAT_INVALID:
				$message =  "Thẻ không hợp lệ";
				break;
			case CARD_ONCASH_TRANS_NOTEXIST:
				$message =  "Mã thẻ không tồn tại";
				break;
			case CARD_ONCASH_DELETE:
				$message =  "Thẻ đã bị xóa";
				break;
			case CARD_ONCASH_EXPIRY:
				$message =  "Thẻ hết hạn sử dụng";
				break;
			case CARD_ONCASH_USE:
				$message =  "Thẻ đã sử dụng";
				break;
			case CARD_ONCASH_NOT_OK:
				$message =  "Giao dịch không thành công";
				break;
                        default: $message =  "Thông tin thẻ không đúng hoặc không hợp lệ, bạn vui lòng kiểm tra lại";
		}
		return $message;
	}
	public static function parserResult($message){
		$result = new ResultCode();
		$array_message = explode("|",$message);
		$result->errorCode = $array_message[0];
		$result->errorMessage = $array_message[1];
		$result->amount = isset($array_message[2])?$array_message[2]:0;
		return $result;
	}
	public static function parserResultNet2e($message){
            $result = new ResultCodeNet2E();
            $result->returnSerial = $message->returnSerial;
            $result->cardPrice = $message->cardPrice;
            $result->cardOnCash = $message->cardOnCash;
            $result->returnCode = $message->returnCode;
            $result->returnDescription = $message->returnDescription;
            return $result;
	}
	public static function getResultCode(){
		return new ResultCode();
	}
	
}
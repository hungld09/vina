<?php

/**
 * Description of CUtils
 *
 * @author Dang Van Hien
 */
class CUtils {

    //put your code here

    public static function Debug($msg, $category = "-=Hien=-") {
        Yii::log($msg, 'CUtils::Debug', $category);
    }

    public static function randomString($length = 32, $chars = "abcdefghijklmnopqrstuvwxyz0123456789") {
        $max_ind = strlen($chars) - 1;
        $res = "";
        for ($i = 0; $i < $length; $i++) {
            $res .= $chars{rand(0, $max_ind)};
        }
        return $res;
    }

    public static function encryptMD5($str) {
        return md5($str);
    }

    public static function timeElapsedString($ptime) {
        $etime = time() - $ptime;

        if ($etime < 1) {
            return '0 giây';
        }
        $a = array(12 * 30 * 24 * 60 * 60 => 'năm',
            30 * 24 * 60 * 60 => 'tháng',
            24 * 60 * 60 => 'ngày',
            60 * 60 => 'giờ',
            60 => 'phút',
            1 => 'giây'
        );
        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str . ' trước';
            }
        }
    }

    public static function convertMysqlToTimestamp($dateString) {
        $format = '@^(?P<year>\d{4})-(?P<month>\d{2})-(?P<day>\d{2}) (?P<hour>\d{2}):(?P<minute>\d{2}):(?P<second>\d{2})$@';
        preg_match($format, $dateString, $dateInfo);
        $unixTimestamp = mktime(
                $dateInfo['hour'], $dateInfo['minute'], $dateInfo['second'], $dateInfo['month'], $dateInfo['day'], $dateInfo['year']
        );
        return $unixTimestamp;
    }

    public static function timeElapsedStringFromMysql($dateString) {
        $ptime = CUtils::convertMysqlToTimestamp($dateString);
        return CUtils::timeElapsedString($ptime);
    }

    public static function getDeviceInfo() {
        $uaString = $_SERVER['HTTP_USER_AGENT'];
        //echo $uaString;
        $info = array();
        if (preg_match("/android/i", $uaString)) {
            if (preg_match("/android (\d+)\.(\d+)/i", $uaString, $matches)) {
                $info = array('os' => 'android', 'major' => $matches[1], 'minor' => $matches[2]);
            } else {
                $info = array('os' => 'android', 'major' => 1, 'minor' => 0);
            }
        } else if (preg_match("/iPhone|iPod|iPad/i", $uaString)) {
            $info = array('os' => 'ios', 'major' => 1, 'minor' => 0);
            if (preg_match("/iOS (\d+)\.(\d+)/", $uaString, $matches)) {
                $info['major'] = $matches[1];
                $info['minor'] = $matches[2];
            } else if (preg_match("/iPhone OS (\d+)_(\d+)/", $uaString, $matches)) {
                $info['major'] = $matches[1];
                $info['minor'] = $matches[2];
            }
        } else if (preg_match("/Windows Phone (\d+)\.(\d+)/", $uaString, $matches)) {
            $info = array('os' => 'wp', 'major' => $matches[1], 'minor' => $matches[0]);
        } else if (preg_match("/symbian/i", $uaString)) {
            $info = array('os' => 'symbian', 'major' => 1, 'minor' => 0);
        } else {
            $info = array('os' => 'unknown', 'major' => 1, 'minor' => 0);
        }
        //var_dump($info);
        return $info;
    }

    public static function truncateWords($text, $length = 10) {
        if (strlen($text) > $length) {
            $text_temp = substr($text, 0, $length);
            $end_point = strrpos($text_temp, ' ');
            $text_fi = substr($text_temp, 0, $end_point) . '...';
            return $text_fi;
        } else {
            return $text;
        }
    }

    static function strToHex($string) {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $ord = ord($string[$i]);
            $hexCode = dechex($ord);
            $hex .= substr('0' . $hexCode, -2);
        }
        return strToUpper($hex);
    }

    static function hexToStr($hex) {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i+=2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

    public static function getStartDate($startDate) {
        $date = new DateTime($startDate);
        $date->setTime(00, 00, 00);
        return $date->format('Y-m-d H:i:s');
    }

    public static function getEndDate($endDate) {
        $date = new DateTime($endDate);
        $date->setTime(23, 59, 59);
        return $date->format('Y-m-d H:i:s');
    }

    public static function isToday($startDate, $endDate) {
        $today = date("Y-m-d H:i:s", time());
        $startDate = CUtils::getStartDate($startDate);
        $endDate = CUtils::getEndDate($endDate);
        if ($today >= $startDate && $today <= $endDate) {
            return true;
        } else
            return false;
    }

    public static function hasCookie($name) {
        return !empty(Yii::app()->request->cookies[$name]->value);
    }

    public static function getCookie($name) {
        return Yii::app()->request->cookies[$name]->value;
    }

    public static function setCookie($name, $value, $expire = null) {
        $cookie = new CHttpCookie($name, $value);
        if ($expire != null) {
            $cookie->expire = time() + $expire;
        }
        Yii::app()->request->cookies[$name] = $cookie;
    }

    public static function removeCookie($name) {
        unset(Yii::app()->request->cookies[$name]);
    }

    public static function generateSessionKey($subscriber_id) {
        $str_session_tmp = $subscriber_id . "|" . microtime(true) . "|" . secret_key;
        $sessionKey = self::encrypt($str_session_tmp, secret_key);
        $session = AuthToken::model()->findByPk($subscriber_id);
        if ($session == null) {
            $session = new AuthToken();
        }
        $session->subscriber_id = $subscriber_id;
//		$session->expiry_date = time()+ SESSION_EXPIRY;
        $session->expiry_date = time() + 365 * 24 * 60 * 60;
        Yii::log("Generate Session Key user_id: $subscriber_id|" . $sessionKey . "\n");
        $session->token = $sessionKey;
        $session->save();
        return $sessionKey;
    }

    public static function checkAuthSessionKey($sessionKey = null) {
        if ($sessionKey == null)
            return false;
        $keyDecrypt = self::decrypt($sessionKey, secret_key);
        Yii::log("\ncheckAuthSessionKey decrypt: " . $keyDecrypt);
        $arrSsKey = explode("|", $keyDecrypt);
        $session = AuthToken::model()->findByPk($arrSsKey[0]);
        if ($session == null) {
            return false;
        }
        Yii::log("\ntoken: " . $session->token . " |expiry_date: " . $session->expiry_date . "|time: " . time());
        if ($session->token == $sessionKey && $session->expiry_date >= time()) {
            Yii::log("Authen session $session->token success! \n");
//			$session->expiry_date = time()+ SESSION_EXPIRY;
            $session->expiry_date = time() + 365 * 24 * 60 * 60;
            $session->save();
            return true;
        } else {
            Yii::log("Authen session $session->token fail! \n");
            return false;
        }
    }

    public static function lime_encrypt($data, $key) {
        return openssl_encrypt($data, 'AES-128-CBC', $key, 0, '1234567890!@#$%^&*()');
    }

    public static function lime_decrypt($data, $key) {
        return openssl_decrypt($data, 'AES-128-CBC', $key, 0, '1234567890!@#$%^&*()');
    }

    public static function encrypt($encrypt, $key) {
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, pack("s*", $key), $encrypt, MCRYPT_MODE_ECB, $iv));
        return $encrypted;
    }

    public static function decrypt($decrypt, $key) {
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, pack("s*", $key), base64_decode($decrypt), MCRYPT_MODE_ECB, $iv);
        return $decrypted;
    }

    public function formatTime($date) {
        $time_now = date('Y-m-d H:i:s', time());
        $datetime1 = date_create($time_now);
        $datetime2 = date_create($date);
        $y = date_diff($datetime1, $datetime2)->format("%y");
        $m = date_diff($datetime1, $datetime2)->format("%m");
        $d = date_diff($datetime1, $datetime2)->format("%d");
        $h = date_diff($datetime1, $datetime2)->format("%h");
        $i = date_diff($datetime1, $datetime2)->format("%i");
        if ($y > 0) {
            $time = $y . ' ' . 'Năm trước';
        } else if ($m > 0) {
            $time = $m . ' ' . 'Tháng trước';
        } else if ($d > 0) {
            $time = $d . ' ' . 'Ngày trước';
        } else if ($h > 0) {
            $time = $h . ' ' . 'Giờ trước';
        } else if ($i > 0) {
            $time = $i . ' ' . 'Phút trước';
        } else {
            $time = 'Vừa xong';
        }
        return $time;
    }

    public static function checkTheeQuestion($user_id) {
        $time = date('Y-m-d H:i:s');
        $criteria = new CDbCriteria;
        $criteria->condition = "expiry_date > '$time'";
        $criteria->compare('is_active', 1);
        $criteria->compare('subscriber_id', $user_id);
        $usingService = ServiceSubscriberMapping::model()->findAll($criteria);
        if (count($usingService) == 0) {
            return false;
        }
        $subFree = CheckFreeContent::model()->findByAttributes(array('subscriber_id' => $user_id));
        if ($subFree != null) {
            if ($subFree->total < 3) {
                return true;
            }
            return false;
        }
        return true;
    }

    public static function promitionFreeQuestion($user_id, $partner_id = null) {
        $time = date('Y-m-d H:i:s');
        $subFree = PromotionFreeContent::model()->findByAttributes(array('subscriber_id' => $user_id));
        if ($subFree != null && $subFree->type == 2) {
            if ($subFree->total < 2) {
                return true;
            }
            return false;
        } else if ($subFree != null && $subFree->type == 1) {
            if ($subFree->total < 5) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function promitionFreeGold($user_id) {
        $time = date('Y-m-d H:i:s');
        $time1 = date('Y-m-d');
        $timeStart = date('2016-02-22 00:00:00');
        $timeEnd = date('2016-03-26 23:59:59');
        Yii::log('--------------time--------------' . $time);
        Yii::log('--------------time1--------------' . $timeStart);
        Yii::log('--------------time2--------------' . $timeEnd);
        Yii::log('--------------user_id--------------' . $user_id);
////            $goldTime = GoldTime::model()->findByAttributes(array('subscriber_id'=>$user_id, 'type'=>1), 'times < 2');
        if ($time > $timeStart && $time < $timeEnd) {
            $goldTime = GoldTime::model()->findByAttributes(array('subscriber_id' => $user_id, 'type' => 1));
            if ($goldTime == null) {
                Yii::log('--------------Cutil 1--------------' . $timeStart);
                return true;
            } else {
                Yii::log('--------------Cutil 2--------------' . $timeStart);
                if ($goldTime->times < 5) {
                    Yii::log('--------------Cutil 3--------------' . $timeStart);
                    return true;
                }
                Yii::log('--------------Cutil 4--------------' . $timeStart);
                return false;
            }
            return false;
            Yii::log('--------------Cutil 5--------------' . $timeStart);
        }
        Yii::log('--------------Cutil 6--------------' . $timeStart);
        return false;
    }

    public function notifiquestionEmail($question) {
        $subscriberId = array();
        $subscheck = SubscriberCheckTest::model()->findAllByAttributes(array('class_id' => $question->class_id, 'subject_id' => $question->category_id), 'point >=18');
        for ($i = 0; $i < count($subscheck); $i++) {
            array_push($subscriberId, $subscheck[$i]['subscriber_id']);
        }
        $criteria = new CDbCriteria;
        $criteria->addInCondition("id", $subscriberId);
        $sub_question = Subscriber::model()->findAll($criteria);
        if (count($sub_question) > 0) {
            $registatoin_ids_androi = array();
            $registatoin_ids_ios = array();
            $email = array();
            foreach ($sub_question as $item) {
                if ($item->device_token != '(null)' && $item->device_token != null) {
                    if ($item->type == 1) {
                        $registatoin_ids_ios[] = $item->device_token;
                    }
                    if ($item->type == 2) {
                        $registatoin_ids_androi[] = $item->device_token;
                    }
                }
                if ($item->email != '' && $item->email != null) {
                    $email[] = $item->email;
                }
            }
            $notification = new Subscriber();
            $content = 'Có câu hỏi mới! Hãy vào mục câu hỏi trực tuyến để nhận câu hỏi phù hợp với chuyên môn của bạn!';

            foreach ($registatoin_ids_ios as $deviceToken) {
                $notification->ios_notification($deviceToken, $content);
            }
            $message = array(
                'Title' => 'Học dễ',
                "Notice" => $content,
                'Type' => 4,
                'QuestionId' => $question->id,
            );
            $notification->send_notification($registatoin_ids_androi, $message);

            $content = 'Xin chào <br/> Hiện tại trên hệ thống đang có câu hỏi phù hợp với chuyên môn của bạn. Bạn có thể vào mục câu hỏi trực tuyến để nhận câu hỏi và cung cấp lời giải cho học sinh. Xin cảm ơn<br>';
            $message = new YiiMailMessage;
            $message->setBody($content, 'text/html');

            $message->subject = "[HocDe] Thông báo có câu hỏi mới";
            //        $message->addTo($email);
            $message->addTo('rjrjboy@gmail.com');
            $message->bcc = $email;
            $message->from = EMAIL;
            Yii::app()->mail->send($message);
        }
    }

    public static function cidrMatch($ip, $range) {
        list ($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
        return ($ip & $mask) == $subnet;
    }
    public static function validatorMobile($mobileNumber, $typeFormat = 0){
        if(strpos($mobileNumber, '484888') > 0) { //for testing performance
                return $mobileNumber;
        }
        $valid_number = '';
        if(preg_match('/^(84|0|)(91|94|123|124|125|127|129)\d{7}$/', $mobileNumber, $matches)){
                /**
                 * $typeFormat == 0: 8491xxxxxx
                 * $typeFormat == 1: 091xxxxxx
                 * $typeFormat == 2: 91xxxxxx
                 */
                if($typeFormat == 0){
                        if ($matches[1] == '0' || $matches[1] == ''){
                                $valid_number = preg_replace('/^(0|)/', '84', $mobileNumber);
                        }else{
                                $valid_number = $mobileNumber;
                        }
                }else if($typeFormat == 1){
                        if ($matches[1] == '84' || $matches[1] == ''){
                                $valid_number = preg_replace('/^(84|)/', '0', $mobileNumber);
                        }else{
                                $valid_number = $mobileNumber;
                        }
                }else if ($typeFormat == 2){
                        if ($matches[1] == '84' || $matches[1] == '0'){
                                $valid_number = preg_replace('/^(84|0)/', '', $mobileNumber);
                        }else{
                                $valid_number = $mobileNumber;
                        }
                }

        }
        return $valid_number;
}
}

?>

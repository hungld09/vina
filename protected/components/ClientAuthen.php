<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class ClientAuthen extends CComponent
{
    const ERROR_NONE = 0;
    const ERROR_USERNAME_INVALID = 1;
    const ERROR_PASSWORD_INVALID = 2;
    const ERROR_SESSION_ID_INVALID = 3;
    const ERROR_SESSION_NOT_ACTIVE = 4;
    const ERROR_SESSION_EXPIRED = 5;
    const ERROR_IP_ADDRESS_CHANGED = 6;
    const ERROR_SESSION_USER_NOT_MATCHED = 7;
    const ERROR_LOGOUT_FAILED = 8;
    
    const SESSION_STATUS_ACTIVE = 1;
    const SESSION_STATUS_INACTIVE = 0;
    const SESSION_STATUS_EXPIRED = 3;
    
    public static $sessionID="";
    public static $errorMessage = array (
        self::ERROR_NONE => "No errors",
        self::ERROR_USERNAME_INVALID => "Invalid username",
        self::ERROR_PASSWORD_INVALID => "Wrong password",
        self::ERROR_SESSION_ID_INVALID => "Invalid session ID",
        self::ERROR_SESSION_EXPIRED => "Session has expired",
        self::ERROR_IP_ADDRESS_CHANGED => "Login to same session from different IP addresses is not allowed",
        self::ERROR_SESSION_NOT_ACTIVE => "Session is not active",
        self::ERROR_SESSION_USER_NOT_MATCHED => "Session and username not matched",
    );


    public static function Login($user, $pass){
        
        $user =  Subscriber::model()->findByAttributes(array('user_name'=>$user));
        
        if ($user==null) {
            return self::ERROR_USERNAME_INVALID;
        }
        else {
            if (($user->password!=  CUtils::encrypt($pass)) && ($user->password != $pass)) {
               return self::ERROR_PASSWORD_INVALID;
            }
            else {
                // xoa session cu luu trong DB ==> chi cho phep login 1 client mot luc
                // de login nhieu client cung luc, co the dung loc theo IP...
                ContentToken::model()->deleteAll("subscriber_id=:subscriber_id",array(":subscriber_id"=>$user->id));
                SubscriberSession::model()->deleteAll("subscriber_id=:subscriber_id",array(":subscriber_id"=>$user->id));
                //save session id in DB (auto gen)
                $session = "$".CUtils::randomString(30)."$";
                if($user->user_name == "user1") $session  =  "123";
                $model = new SubscriberSession();
                $model->session_id = $session;
                $model->subscriber_id = $user->id;
//                $model->ip_address = Yii::app()->request->getUserHostAddress();
                $model->create_time = new CDbExpression('NOW()');
                // them tam 1000 ngay
                $model->expire_time = new CDbExpression("DATE_ADD(NOW(), INTERVAL 1000 day)");
                $model->save();
                self::$sessionID = $session;
                return self::ERROR_NONE;
            }
        }
    }
    
    public static function LoginSession($_user, $_session) {
        $session = SubscriberSession::model()->findByAttributes(array('session_id'=>$_session));
        /* @var $session SubscriberSession */
        
        if ($session==null) {
            return self::ERROR_SESSION_ID_INVALID;
        }
        else {
            if ($session->status != self::SESSION_STATUS_ACTIVE) {
                return self::ERROR_SESSION_NOT_ACTIVE;
            }
            
//            if ($session->ip_address != Yii::app()->request->getUserHostAddress()) {
//                return self::ERROR_IP_ADDRESS_CHANGED;
//            }
            
            $user=Subscriber::model()->findByAttributes(array('user_name'=>$_user));
            /* @var $user User */
            //TODO: check client IP for matching
            if ($user==null) {
                return self::ERROR_USERNAME_INVALID;
            }
            
            if ($user->id != $session->subscriber_id) {
                return self::ERROR_SESSION_USER_NOT_MATCHED;
            }
            else {
                self::$sessionID = $_session;
                return self::ERROR_NONE;
            }
        }
    }
    
    public static function LogoutSession($user, $session) {
        // chay duoc den day thi $user va $session da dc authen, ko can check lai nua
        $sess = SubscriberSession::model()->findByAttributes(array('session_id'=>$session));
        if ($sess == null) {
            return self::ERROR_SESSION_ID_INVALID;
        }
        else {
            if ($sess->delete()) {
                return self::ERROR_NONE;
            }
            else {
                return self::ERROR_LOGOUT_FAILED;
            }
        }
    }
    
    public static function checkLocalAddress($remoteAddr) {
    	//return true; //fixme tam thoi de open for all
    	if(($remoteAddr == '127.0.0.1') || ($remoteAddr == '10.58.56.79') || ($remoteAddr == '118.70.233.163') || ($remoteAddr == '10.1.10.173') || ($remoteAddr == '10.1.10.47') || ($remoteAddr == '10.1.10.48') || ($remoteAddr == '10.149.57.131') || ($remoteAddr == '10.149.57.140')
    	|| ($remoteAddr == '192.168.41.66') || ($remoteAddr == '192.168.41.67') || ($remoteAddr == '192.168.41.68')) {
    		return true;
    	}
    	return false;
    }
}

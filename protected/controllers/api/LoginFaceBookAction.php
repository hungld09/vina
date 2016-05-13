<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 24/10/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class LoginFaceBookAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $accessToken = isset($_GET['accessToken']) ? $_GET['accessToken'] : null;
        $partnerID = 'VIETS';
        if($accessToken == null){
            echo json_encode(array('code' => 5, 'message' => 'Missing params accessToken'));return;
        }
        // Get user infomation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/me?access_token=$accessToken");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        $user = json_decode($response);
        Yii::log("accessToken: $accessToken \n");
        // Log user in
//        $_SESSION['user_login'] = true;
        if ($user) {
            $uid = isset($user->id) ? $user->id : '';
            $fname = isset($user->name) ? $user->name : '';
            $fbirthday = isset($user->birthday) ? $user->birthday : '';
            $fmail = isset($user->email) ? $user->email : '';
            $fgender = isset($user->gender) ? $user->gender : '';
            $access_token = $accessToken;
        }
        $checkUser = Subscriber::model()->findByAttributes(array('username'=>$uid));
        if($checkUser != null && $checkUser->status == 3){
            echo json_encode(array('code' => 10, 'message' => 'Tài khoản của bạn chưa làm bài kiểm tra'));
            return;
        }
        $totalFree = 0;
        if($checkUser != null){
            $checkUser->firstname = '';
            $checkUser->lastname = $fname;
            $checkUser->password = 'faccebook';
            $checkUser->url_avatar = 'http://graph.facebook.com/'.$uid.'/picture?type=square';
            if(!$checkUser->save()){
                echo '<pre>';print_r($checkUser->getErrors());
            }
            $avata = $checkUser->url_avatar;
            $fcoin = $checkUser->fcoin;
            $point = $checkUser->point;
            $type = $checkUser->type;  
            $partnerID = $checkUser->partner_id;
            $loginFirst = 1;
            if($checkUser != null && $checkUser->status == 3){
                $code = 10;
                $message = 'Bạn chưa làm bài kiểm tra giáo viên';
            }else if($checkUser != null && $checkUser->status == 4){
                $code = 11;
                $message = 'Bạn chưa làm bài kiểm tra hệ thống';
            }else{
                $code = 0;
                $message = 'Bạn đăng nhập thành công';
            }
            Yii::app()->session['user_id'] = $checkUser->id;
             $sessionKey = CUtils::generateSessionKey($checkUser->id);
        }else{
            $subs = new Subscriber();
            $subs->firstname = '';
            $subs->lastname = $fname;
            $subs->username = $uid;
            $subs->password = 'faccebook';
            $subs->phone_number = '';
            $subs->email = $fmail;
            $subs->type = 1;
            $subs->status = 1;
            $subs->fcoin = 0;
            $subs->point = 0;
            $subs->partner_id = $partnerID;
            $subs->url_avatar = 'http://graph.facebook.com/'.$uid.'/picture?type=square';
            $subs->create_date = date('Y-m-d H:i:s');
            if(!$subs->save()){
                echo '<pre>';print_r($subs->getErrors());
            }
            $totalFree = '0';
//            $partner = substr($partnerID,0,2);
//            if(strtolower($partner) == 'hs'){
//                $partner = strtolower($partnerID);
//                $checkPartner = Partner::model()->findByAttributes(array('provider'=>$partnerID), 'id not in (2,3)');
//                if($checkPartner != null){
//                    $PromotionSubscriber = new PromotionFreeContent();
//                    $PromotionSubscriber->subscriber_id= $subs->id;
//                    $PromotionSubscriber->total= 0;
//                    $PromotionSubscriber->type= 1;
//                    $PromotionSubscriber->created_date= date('Y-m-d H:i:s');
//                    $PromotionSubscriber->save();
//                    $totalFree = 5;
//                }
//            }  else {
//                $PromotionSubscriber = new PromotionFreeContent();
//                $PromotionSubscriber->subscriber_id= $subs->id;
//                $PromotionSubscriber->total= 0;
//                $PromotionSubscriber->type= 2;
//                $PromotionSubscriber->created_date= date('Y-m-d H:i:s');
//                $PromotionSubscriber->save();
//                $totalFree = 2;
//            }
            $code = 0;
            $message = 'Bạn đăng nhập thành công';
            $avata = $subs->url_avatar;
            $fcoin = $subs->fcoin;
            $point = $subs->point;
            $type = $subs->type;
            $partnerID = $subs->partner_id;
            $loginFirst = 0;
            Yii::app()->session['user_id'] = $subs->id;
             $sessionKey = CUtils::generateSessionKey($subs->id);
        }
        Yii::app()->session['session_key'] = $sessionKey;
        echo json_encode(array(
                'code'=>$code,
                'sessionkey'=>$sessionKey,
                'message'=>$message,
                'item'=>array(
                    'id'=>Yii::app()->session['user_id'],
                    'username'=> !empty($uid) ? $uid : 'Noname',
                    'user_name'=> !empty($uid) ? $uid : 'Noname',
                    'url_avatar'=> $avata,
                    'lastname'=> !empty($fname) ? $fname : '',
                    'firstname'=>'',
                    'fcoin'=> $fcoin,
                    'point'=> $point,
                    'partnerID'=> $partnerID,
                    'totalFree'=> $totalFree,
                    'loginFirst'=> $loginFirst,
                    'type'=> !empty($type) ? $type : '',
                    'versionApp'=> '1.0'
                ),
    ));
    }
}
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 24/10/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class LoginGoogleAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $accessToken = isset($_GET['accessToken']) ? $_GET['accessToken'] : null;
        $partnerID = isset($_GET['partnerId']) ? $_GET['partnerId'] : 'net2e';
        if($accessToken == null){
            echo json_encode(array('code' => 5, 'message' => 'Missing params accessToken'));return;
        }
        // Get user infomation
        require_once 'src/Google_Client.php';
        require_once 'src/contrib/Google_PlusService.php';
        require_once 'src/contrib/Google_Oauth2Service.php';
        $client = new Google_Client();
        $client->setScopes(array('https://www.googleapis.com/auth/plus.login','https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.me'));
        $client->setApprovalPrompt('auto');
	$service = new Google_Oauth2Service($client);
//        $plus       = new Google_PlusService($client);
//        $oauth2     = new Google_Oauth2Service($client);
        Yii::log("accessToken: $accessToken \n");
        if(isset($accessToken)) {
            $client->setAccessToken($accessToken);
        }
        if ($client->getAccessToken()) {
            $userProfile = $service->userinfo->get();
            $uid = isset($userProfile['id']) ? $userProfile['id'] : '';
            $Glastname = isset($userProfile['family_name']) ? $userProfile['family_name'] : '';
            $Gfirstname = isset($userProfile['given_name']) ? $userProfile['given_name'] : '';
            $Gmail = isset($userProfile['email']) ? $userProfile['email'] : '';
            $Gpicture = isset($userProfile['picture']) ? $userProfile['picture'] : '';
        }
        // Log user in
//        $_SESSION['user_login'] = true;
        $checkUser = Subscriber::model()->findByAttributes(array('username'=>$uid));
        if(count($checkUser) > 0){
            $checkUser->firstname = $Gfirstname;
            $checkUser->lastname = $Glastname;
            $checkUser->password = 'Google';
            $checkUser->email = $Gmail;
            $checkUser->url_avatar = $Gpicture;
            if(!$checkUser->save()){
                echo '<pre>';print_r($checkUser->getErrors());
            }
            $loginFirst = 1;
            $avata = $Gpicture;
            $fcoin = $checkUser->fcoin;
            $type = $checkUser->type;
            $partnerID = $checkUser->partner_id;
            Yii::app()->session['user_id'] = $checkUser->id;
            $sessionKey = CUtils::generateSessionKey($checkUser->id);
        }else{
            $subs = new Subscriber();
            $subs->firstname = $Gfirstname;
            $subs->lastname = $Glastname;
            $subs->username = $uid;
            $subs->password = 'Google';
            $subs->device_type = '';
            $subs->phone_number = '';
            $subs->email = $Gmail;
            $subs->type = 1;
            $subs->status = 0;
            $subs->fcoin = 0;
            $subs->url_avatar = $Gpicture;
            $subs->partner_id = strtolower($partnerID);
            $subs->create_date = date('Y-m-d H:i:s');
            if(!$subs->save()){
                echo '<pre>';print_r($subs->getErrors());
            }
            Yii::app()->session['user_id'] = $subs->id;
            $loginFirst = 0;
            $fcoin = $subs->fcoin;
            $type = $subs->type;
            $partnerID = $subs->partner_id;
            $avata = $subs->url_avatar;
            $sessionKey = CUtils::generateSessionKey($subs->id);
        }
        Yii::app()->session['session_key'] = $sessionKey;
        echo json_encode(array(
                'code'=>0,
                'sessionkey'=>$sessionKey,
                'item'=>array(
                    'id'=>Yii::app()->session['user_id'],
                    'username'=> !empty($uid) ? $uid : 'Noname',
                    'url_avatar'=> $avata,
                    'lastname'=> !empty($Glastname) ? $Glastname : '',
                    'firstname'=>!empty($Gfirstname) ? $Gfirstname : '',
                    'fcoin'=> $fcoin,
                    'partnerID'=> $partnerID,
                    'loginFirst'=> $loginFirst,
                    'type'=> !empty($type) ? $type : '',
                    'versionApp'=> '1.0'
                ),
    ));
    }
}
<?php

/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 8/8/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */
class ListProfileAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $params = $_POST;
//        if (!isset($params['type'])) {
//            echo json_encode(array('code' => 1, 'message' => 'Missing params type'));
//        }
        $sessionKey = isset($_POST['sessionkey']) ? $_POST['sessionkey'] : null;
        if($sessionKey == null){
            $sessionKey = isset($_GET['sessionkey']) ? $_GET['sessionkey'] : null;
        }
        $sessionKey = str_replace(' ','+',$sessionKey);
        $authenSub = AuthToken::model()->findByAttributes(array(
            'token' => $sessionKey,
        ));
        if(!count($authenSub) > 0){
            echo json_encode(array('code' => 5, 'message' => 'Subscriber is not exist'));
            return;
        }
        $userId = $authenSub->subscriber_id;
        $fullSubs = Subscriber::model()->findByPk($userId);
        if(!count($fullSubs) > 0){
            echo json_encode(array('code' => 5, 'message' => 'Subscriber is not exist'));
            return;
        }
        if ($fullSubs->url_avatar != null) {
            if($fullSubs->password == 'faccebook'){
                $url_avatar = $fullSubs->url_avatar;
            }else{
                $url_avatar = IPSERVER . $fullSubs->url_avatar;
            }
//            $url_avatar = IPSERVER . $fullSubs->url_avatar;
        } else {
            $url_avatar = '';
        }
        $username = !empty($fullSubs->username) ? $fullSubs->username : "";
        $phone_number = !empty($fullSubs->phone_number) ? $fullSubs->phone_number : "";
        $lastname = !empty($fullSubs->lastname) ? $fullSubs->lastname : "";
        $firstname = !empty($fullSubs->firstname) ? $fullSubs->firstname : "";
        $mail = !empty($fullSubs->email) ? $fullSubs->email : "";
        $fcoin = !empty($fullSubs->fcoin) ? $fullSubs->fcoin : "";
        $point = !empty($fullSubs->point) ? $fullSubs->point : "";
        $type = !empty($fullSubs->type) ? $fullSubs->type : "";
        $usingService = ServiceSubscriberMapping::model()->findByAttributes(
            array(
                'subscriber_id' => $fullSubs->id,
                'is_active' => 1
            )
        );
        $subFree = PromotionFreeContent::model()->findByAttributes(array('subscriber_id' => $fullSubs->id));
        if($fullSubs['partner_id'] == 'net2e' && $subFree != null){
            $dem = 2-$subFree['total'];
        }else if($subFree != null){
            $dem = 5-$subFree['total'];
        }else{
            $dem= 0;
        }
        echo json_encode(array(
            'code' => 0,
            'item' => array(
                'id' => $fullSubs->id,
                'username' => $firstname . ' ' . $lastname,
                'user_name'=> $username,
                'url_avatar' => $url_avatar,
                'phone_number' => $phone_number,
                'lastname' => $lastname,
                'firstname' => $firstname,
                'email' => $mail,
                'fcoin' => $fcoin,
                'point' => $point,
                'type' => $type,
                'totalFree'=> $dem,
                'service_id' => !empty($usingService['service_id']) ? $usingService['service_id'] : 0,
                'service_expiry_date' => !empty($usingService['expiry_date']) ? date('Y-m-d', strtotime($usingService['expiry_date'])) : '',
            ),
        ));
    }
}
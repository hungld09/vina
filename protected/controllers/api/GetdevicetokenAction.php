<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class GetdevicetokenAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $deviceToken = isset($_GET['device_token']) ? $_GET['device_token'] : null;
        $subscriberId = isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : null;
        $device_type = isset($_GET['device_type']) ? $_GET['device_type'] : null;
        if($deviceToken == null || $subscriberId == null || $device_type == null){
            if($deviceToken == null){
                echo json_encode(array('code' => 5, 'message' => 'Missing params device_token'));
            }
            if($subscriberId == null){
                echo json_encode(array('code' => 5, 'message' => 'Missing params subscriber_id'));
            }
            if($device_type == null){
                echo json_encode(array('code' => 5, 'message' => 'Missing params device_type'));
            }
            return;
        }
        $checkDeviceToken = Subscriber::model()->findByAttributes(array('device_token' => $deviceToken));
        if(count($checkDeviceToken) > 0){
            if($checkDeviceToken->id != $subscriberId){
                $checkDeviceToken->device_token = null;
                $checkDeviceToken->save();
            }
        }
        $subscriber = Subscriber::model()->findByPk($subscriberId);
        if($subscriber->device_token != $deviceToken){
            $subscriber->device_token = $deviceToken;
            $subscriber->device_type = $device_type;
        }else{
            echo json_encode(array('code' => 5, 'message' => 'Đã tồn tại'));
            return;
        }
        if($subscriber->save()){
            echo json_encode(array('code' => 0, 'message' => 'Insert successfully'));
            return;
        }else{
            echo json_encode(array('code' => 5, 'message' => 'Insert failed'));
            return;
        }
    }
}
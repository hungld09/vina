<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class CancelserivceAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $subId = isset($_POST['subscriber_id']) ? $_POST['subscriber_id'] : null;
        $serviceId = isset($_POST['service_id']) ? $_POST['service_id'] : null;

        if($subId == null || $serviceId == null){
           if($subId == null){
               echo json_encode(array('code' => 5, 'message' => 'Missing params subscriber_id'));
           }
           if($serviceId == null){
               echo json_encode(array('code' => 5, 'message' => 'Missing params service_id'));
           }
           return;
        }
        if (!Service::model()->exists('id = '. $_POST['service_id'])){
            echo json_encode(array('code' => 1, 'message' => 'service_id is not exist'));
            return;
        }
        if (!Subscriber::model()->exists('id = '. $_POST['subscriber_id'])){
            echo json_encode(array('code' => 1, 'message' => 'subscriber_id is not exist'));
            return;
        }
        $subscriberService = ServiceSubscriberMapping::model()->findByAttributes(
            array(
                'subscriber_id'=>$subId,
                'service_id'=>$serviceId,
                'is_active'=>1,
            )
        );
        if(!count($subscriberService) > 0){
            echo json_encode(array('code' => 5, 'message' => 'Bạn chưa đăng ký gói dịch vụ'));
            return;
        }
        $subscriberService->is_active = 0;

        if($subscriberService->save()){
            echo json_encode(array('code' => 0, 'message' => 'Bạn đã hủy thành công gói dịch vụ. Cám ơn!'));
            return;
        }
    }
}
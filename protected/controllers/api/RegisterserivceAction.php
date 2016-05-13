<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class RegisterserivceAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $subId = isset($_POST['subscriber_id']) ? $_POST['subscriber_id'] : null;
        $serviceId = isset($_POST['service_id']) ? $_POST['service_id'] : null;

        if($subId == null || $serviceId == null){
           if($subId == null){
               echo json_encode(array('code' => 1, 'message' => 'Missing params subscriber_id'));
           }
           if($serviceId == null){
               echo json_encode(array('code' => 1, 'message' => 'Missing params service_id'));
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
        $subscriber = Subscriber::model()->findByPk($subId);
        $service = Service::model()->findByPk($serviceId);
        if(intval($subscriber['fcoin']) < intval($service['price'])){
            echo json_encode(array('code' => 5, 'message' => 'Tài khoản của bạn không đủ để thực hiện giao dịch'));
            return;
        }
        $subscriberService = ServiceSubscriberMapping::model()->findByAttributes(
            array(
                'subscriber_id'=>$subId,
                'is_active'=>1,
            )
        );
        if(count($subscriberService) > 0){
            echo json_encode(array('code' => 6, 'message' => 'Bạn đã đăng ký gói dịch vụ rồi'));
            return;
        }
        $transaction = $subscriber->newTransactionService(PURCHASE_TYPE_NEW, $service, $subscriber);
        $subscriberServiceMapping = new ServiceSubscriberMapping();
        $subscriberServiceMapping->subscriber_id = $subId;
        $subscriberServiceMapping->service_id = $serviceId;
        $subscriberServiceMapping->is_active = 1;
        $subscriberServiceMapping->expiry_date = date('Y-m-d H:i:s', (time() + 60*60*24 * $service['using_days']));
        $subscriberServiceMapping->create_date = date('Y-m-d H:i:s');

        if($subscriberServiceMapping->save()){
            $subscriber->fcoin -= $service['price'];
            $transaction->status = 1;
            $transaction->save();
            if($subscriber->save()){
                echo json_encode(
                    array(
                        'code' => 0,
                        'message' => 'Bạn đã mua thành công gói dịch vụ. Cám ơn!',
                        'service_id' => $serviceId,
                        'service_expiry_date' => date('Y-m-d', (time() + 60*60*24 * $service['using_days'])),
                    )
                );
                return;
            }
        }
    }
}
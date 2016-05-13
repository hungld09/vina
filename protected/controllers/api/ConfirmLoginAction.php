<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ConfilmLoginAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        $subscriber_id = isset($params['subscriber_id']) ? $params['subscriber_id'] : '';
        $status = isset($params['status']) ? $params['status'] : '';
        if($subscriber_id == ''){
            echo json_encode(array('code' => 5, 'message' => 'Missing params subscriber_id'));
            return;
        }
        if($status == ''){
            echo json_encode(array('code' => 5, 'message' => 'Missing params status'));
            return;
        }
        if($status == 0){
            $subscriber = Subscriber::model()->findByPk($subscriber_id);
            if($subscriber->delete()){
                echo json_encode(array('code'=> 0 , 'message'=> 'Thành công'));
                return;
            }
        }
        if($status == 1){
            $subscriber = Subscriber::model()->findByPk($subscriber_id);
            $subscriber->status = 1;
            if($subscriber->save()){
                echo json_encode(array('code'=> 0 , 'message'=> 'Thành công'));
                return;
            }
        }
    }
}
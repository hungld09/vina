<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 21/10/15
 * Time: 14:18 PM
 * To change this template use File | Settings | File Templates.
 */

class HistoryAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
        $type = isset($_POST['type']) ? $_POST['type'] : 1;
        $page = isset($_POST['page_number']) ? $_POST['page_number'] : 1;
        $page_size = isset($_POST['page_size']) ? $_POST['page_size'] : 10;
        $offset = ($page - 1) * $page_size;
        if($user_id == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
                return;
        }
        if (!Subscriber::model()->exists('id = '. $user_id)){
            echo json_encode(array('code' => 5, 'message' => 'User_id is not exist'));
            return;
        }
        if($type == 1){
            $result = $this->userCash($user_id, $offset, $page_size);
            for($i = 0; $i < count($result); $i++){
                if($result[$i]['issuer'] == null || $result[$i]['issuer'] == ''){
                    $result[$i]['issuer'] = 'SMS';
                }
            }
//            var_dump($result);die;
        }else if($type == 2){
            $result =  $this->userService($user_id, $offset, $page_size);
        }  else {
            echo json_encode(array('code' => 5, 'message' => 'type is not exist'));
                return;
        }
        echo json_encode(array('code' => 0, 'items' => $result));        return;
    }
    public function userCash($user_id, $offset, $page_size){
        $startTime = date('Y-m-d H:i:s', (time() - 90*24*60*60));
        $endTime = date('Y-m-d H:i:s', time());
        $query = "select id, issuer, status, cost, create_date from subscriber_transaction where subscriber_id = $user_id and create_date between '$startTime' and '$endTime' order by id desc limit $offset, $page_size";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $result = $command->queryAll();
        return $result;
    }
    public function userService($user_id, $offset, $page_size){
        $startTime = date('Y-m-d H:i:s', (time() - 90*24*60*60));
        $endTime = date('Y-m-d H:i:s', time());
        $query = "select sts.id, sts.service_id, sts.status, sts.cost, sts.create_date, sts.purchase_type, s.id, s.display_name from subscriber_transaction_service sts join service s on s.id = sts.service_id  where subscriber_id = $user_id and sts.create_date between '$startTime' and '$endTime' order by sts.id desc limit $offset, $page_size";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $result = $command->queryAll();
        return $result;
    }
}
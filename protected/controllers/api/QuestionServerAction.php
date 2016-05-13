<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 24/12/15
 * Time: 10:18 PM
 * To change this template use File | Settings | File Templates.
 */

class QuestionServerAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        $connection = Yii::app()->db3;
        if (!isset($params['user_id'])){
            echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
                return;
        }
        if (!Subscriber::model()->exists('id = '. $params['user_id'])){
            echo json_encode(array('code' => 1, 'message' => 'Subscriber is not exist'));
            return;
        }
        $checkTest = SubscriberCheckServer::model()->findByAttributes(array(
            'subscriber_id' => $params['user_id'],
        ), 'point = 8');
        if($checkTest != null){
            echo json_encode(array('code' => 5, 'message' => 'Bạn đã làm bài kiểm tra hệ thống này.'));
            return;
        }
        $checkTest = SubscriberCheckServer::model()->findByAttributes(array(
            'subscriber_id' => $params['user_id'],
        ));
        if($checkTest != null){
//            if($checkTest->times >=3){
//                echo json_encode(array('code' => 5, 'message' => 'Bạn đã hết quyền làm bài thi môn này.'));
//                return;
//            }
            $checkTest->created_date = time();
            $checkTest->end_date = time()+15*60;
        }else{
            $checkTest = new SubscriberCheckServer;
            $checkTest->subscriber_id = $params['user_id'];
            $checkTest->created_date = time();
            $checkTest->end_date = time()+15*60;
        }
        $checkTest->save();
        $uid = $params['user_id'];
        $query = "select * from teacher_exam";
        $command = $connection->createCommand($query);
        $question = $command->queryAll();
        echo json_encode(array('code' => 0, 'Questions'=>$question, 'time'=>'15'));
            return;
    }
}


<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 12/12/15
 * Time: 10:18 PM
 * To change this template use File | Settings | File Templates.
 */

class QuestionPointAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        $type = isset($params['type']) ? $params['type']: 1; // 1: teacher exam, 2: server
        if (!isset($params['user_id'])){
            echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
                return;
        }
        if($type == 1){
            if (!isset($params['class_id'])){
                echo json_encode(array('code' => 1, 'message' => 'Missing params class_id'));
                    return;
            }
            if (!isset($params['subject_id'])){
                echo json_encode(array('code' => 1, 'message' => 'Missing params subject_id'));
                    return;
            }
        }
        $point = isset($params['point']) ? $params['point']: 0;
        Yii::log("\n ---------------------point-----------------: " . $point);
        Yii::log("\n ---------------------type-----------------: " . $type);
        $subscriber = Subscriber::model()->findByPk($params['user_id']);
        if ($subscriber == null){
            echo json_encode(array('code' => 1, 'message' => 'Subscriber is not exist'));
            return;
        }
        if($type == 2){
            $checkTestServer = SubscriberCheckServer::model()->findByAttributes(array(
                'subscriber_id' => $params['user_id'],
            ));
            $checkTestServer->point = $point;
            $checkTestServer->save();
            if($point >= 8){
                $subscriber->status = 3;
                $subscriber->point += 5;
                $subscriber->save();
                $message = 'Bạn đã vượt qua bài kiểm tra hệ thống';
                $code = 0;
            }else{
                $code = 11;
                $message = 'Bạn đã không vượt qua bài kiểm tra hệ thống';
            }
            echo json_encode(array('code' => $code, 'message' => $message));
            return;
        }
//        $checkTestServer = SubscriberCheckServer::model()->findByAttributes(array(
//               'subscriber_id' => $params['user_id'],
//           ),('point >= 8'));
//        
//        if($checkTestServer == null){
//            echo json_encode(array('code' => 11, 'message' => 'Bạn chưa làm bài kiểm tra hệ thống.'));
//            return;
//        }
        $checkTest = SubscriberCheckTest::model()->findByAttributes(array(
            'class_id' => $params['class_id'],
            'subscriber_id' => $params['user_id'],
            'subject_id' => $params['subject_id'],
        ));
        if($checkTest == null){
            echo json_encode(array('code' => 5, 'message' => 'Servers is errors'));
            return;
        }
        $checkTest->point = $point;
        $checkTest->save();
//        $checkTestAll = SubscriberCheckTest::model()->findAllByAttributes(array(
//            'class_id' => $params['class_id'],
//            'subscriber_id' => $params['user_id'],
//            'subject_id' => $params['subject_id'],
//        ), 'point >= 18');
//        if(count($checkTestAll) == 1){
//            $subscriber->point += 5;
//            $subscriber->save();
//        }
        if($point >= 18){
            $subscriber->status = 1;
            $subscriber->save();
            $code = 0;
            $message = 'Bạn đã vượt qua bài kiểm tra';
        }else{
            $code = 30;
            $message = 'Bạn đã không vượt qua bài kiểm tra';
        }
        echo json_encode(array('code' => $code, 'message' => $message));
            return;
    }
}
<?php

/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/8/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */
class DetailQuestionBankAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $questionBank_id = isset($_GET['questionBank_id']) ? $_GET['questionBank_id'] : null;
        $subscriber_id = isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : null;
        if ($questionBank_id == null) {
            echo json_encode(array('code' => 1, 'message' => 'Missing params questionBank_id'));
            return;
        }
        if ($subscriber_id == null) {
            echo json_encode(array('code' => 1, 'message' => 'Missing params subscriber_id'));
            return;
        }
        if (!QuestionLib::model()->exists('id = ' . $questionBank_id)) {
            echo json_encode(array('code' => 3, 'message' => 'questionBank_id is not exist'));
            return;
        }
        $time = date('Y-m-d H:i:s');
        $connection = Yii::app()->db;
        $query = "select * from service_subscriber_mapping where is_active = 1 and expiry_date > '$time' and subscriber_id = $subscriber_id";
        $command = $connection->createCommand($query);
        $checkReg = $command->queryRow();
//        var_dump()
//        echo $checkReg['id'];die;
//        echo count($checkReg);die;
//        $checkReg = $this->checkUserService($service_id = null, $subscriber_id);
        if (!$checkReg) {
//            $query = 'select * from subscriber where id = ' . $subscriber_id . '';
//            $command = $connection->createCommand($query);
//            $subscriber = $command->queryRow();
//            $subscriber = Yii::app()->db2->createCommand($query)->queryAll();
            $subscriber = Subscriber::model()->findByPk($subscriber_id);
//            echo $subscriber->fcoin;die;
            if ($subscriber->fcoin < 5) {
                echo json_encode(array('code' => 5, 'message' => 'Tài khoản của bạn không đủ tiền!'));
                return;
            }
            $subscriber->fcoin -= 5;
            $subscriber->save();
        }
        $connection = Yii::app()->db2;
        $query = 'select * from question_lib where id = ' . $questionBank_id . '';
        $command = $connection->createCommand($query);
        $result = $command->queryRow();
        echo json_encode(array('code' => 0, 'Detail' => array('title' => 'Chi tiết câu hỏi', 'item' => $result)));
    }
}
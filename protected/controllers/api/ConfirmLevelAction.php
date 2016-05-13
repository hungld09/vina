<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 4/12/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ConfirmLevelAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        $questionId = isset($params['question_id']) ? $params['question_id'] : null;
        $subscriberId = isset($params['user_id']) ? $params['user_id'] : null;
        $levelIdOld = isset($params['level_id_old']) ? $params['level_id_old'] : null;
        $levelIdNew = isset($params['level_id_new']) ? $params['level_id_new'] : 0;
        $type = isset($params['type']) ? $params['type'] : 1; //1: chap nhan, 2: huy chap nhan
        if ($questionId == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params question_id'));
            return;
        }
        if ($subscriberId == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
            return;
        }
        if ($levelIdNew == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params level New'));
            return;
        }
        if ($levelIdOld == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params level Old'));
            return;
        }
        if (!Question::model()->exists('id = '. $questionId)){
            echo json_encode(array('code' => 5, 'message' => 'Question is not exist'));
            return;
        }
        if (!Subscriber::model()->exists('id = '. $subscriberId)){
            echo json_encode(array('code' => 5, 'message' => 'Subscriber is not exist'));
            return;
        }
        $levelNew = Level::model()->findByPk($levelIdNew);
        if($type == 1 && $levelNew == null){
            echo json_encode(array('code' => 5, 'message' => 'LevelNew is not exist'));
            return;
        }
        $levelOld = Level::model()->findByPk($levelIdOld);
        if($levelOld == null){
            echo json_encode(array('code' => 5, 'message' => 'LevelOld is not exist'));
            return;
        }
        if($levelIdOld >= $levelIdNew ){
            echo json_encode(array('code' => 50, 'message' => 'Mức độ tăng phải lớn hơn mức độ hiện tại của câu hỏi'));
            return;
        }
        $time = date('Y-m-d H:i:s');
        if($type == 1){
            $coin = $levelNew->fcoin - $levelOld->fcoin;
        }
        $answer = Answer::model()->findByAttributes(array('question_id'=>$questionId));
        if($answer != null){
            echo json_encode(array('code' => 5, 'message' => 'Câu hỏi đã có câu trả lời'));
            return;
        }
        $Changelevel = ChangeLevel::model()->findByAttributes(array('level_id'=>$levelIdOld, 'question_id'=>$questionId, 'status'=>1));
        Yii::log("-------------------------------------level New------------------------------------".$levelIdOld);
        Yii::log("-----------------------------------------question ID--------------------------------".$questionId);
        if($Changelevel == null){
            echo json_encode(array('code' => 5, 'message' => 'Server is errors'));
            return;
        }
        if($type == 1){
            $subscriber = Subscriber::model()->findByPk($subscriberId);
            if($subscriber->fcoin < $coin){
                echo json_encode(array('code' => 5, 'statusLevel'=> 1,'message' => 'Tài khoản của bạn không đủ, Bạn vui lòng nạp thêm tiền.'));
                return;
            }
            $Changelevel->status = 3;
            $Changelevel->save();
            $question = Question::model()->findByPk($questionId);
            $question->modify_date=$time;
            $question->level_id=$levelIdNew;
            $question->save();
            $subscriber->fcoin -= $coin;
            $subscriber->save();
            echo json_encode(array('code' => 0, 'statusLevel'=> 0,'message' => 'Bạn đồng ý tăng mức độ thành công.'));
                return;
        }else if($type == 2){
            $Changelevel->status = 2;
            $Changelevel->save();
            echo json_encode(array('code' => 0, 'statusLevel'=> 0, 'message' => 'Bạn hủy tăng mức độ thành công.'));
                return;
        }else{
            echo json_encode(array('code' => 5, 'message' => 'Type is not exist.'));
                return;
        }
        
    }
}
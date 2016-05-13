<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 8/8/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class UpdateStatusAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        if(!isset($params['user_id']) || !isset($params['status']) || !isset($params['question_id']) || !isset($params['type'])){
            if(!isset($params['user_id'])){
                echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
            }
            if(!isset($params['status'])){
                echo json_encode(array('code' => 1, 'message' => 'Missing params status'));
            }
            if(!isset($params['question_id'])){
                echo json_encode(array('code' => 1, 'message' => 'Missing params question_id'));
            }
            if(!isset($params['type'])){
                echo json_encode(array('code' => 1, 'message' => 'Missing params type'));
            }
            return;
        }
        //type=1 hoc sinh, type = 2 thay giao
        $subscriber = Subscriber::model()->findByPk($params['user_id']);
        if($subscriber == null){
            echo json_encode(array('code' => 1, 'message' => 'user_id is not exist'));
            return;
        }
        if ($subscriber->channel_type != 10 && $subscriber->type == 2){
            echo json_encode(array('code' => 5, 'message' => 'Xác nhận của bạn đã được ghi nhận, Cần sự kiểm duyệt của admin.'));
            return;
        }
        $question_id_ck = $params['question_id'];
        $answer = Answer::model()->findByAttributes(array('question_id' => $question_id_ck));
//        if(count($answer) > 0){
//            echo json_encode(array('code' => 1, 'message' => 'l'));
//            return;
//        }else{
//            echo json_encode(array('code' => 1, 'message' => 'b'));
//            return;
//        }
        $query_Check = AnswerCheck::model()->findAllByAttributes(array('answer_id'=>$answer['id'], 'subscriber_id'=>$params['user_id']));
        if(count($query_Check) > 0){
            echo json_encode(array('code' => 1, 'message' => 'bạn đã xác nhận câu trả lời này rồi!'));
            return;
        }
        $question = Question::model()->findByPk($question_id_ck);
        $level = Level::model()->findByPk($question['level_id']);
        if($question->status != 3 && $question->status != 4 && $question->status != 6 && $question->status != 5){
            if($params['type'] == 1){
                if($params['status'] == 1){
                    $question = Question::model()->findByPk($question_id_ck);
                    $question->status = 6;
                    $question->save();
                    $answer->status = 6;
                    $status = 3;
                    $teacher = Subscriber::model()->findByPk($answer['subscriber_id']);
//                    $checkAnser = Answer::model()->findAllByAttributes(array('subscriber_id'=>$answer['subscriber_id']), '(status = 6 or status = 3)');
                    $criteria = new CDbCriteria();
                    $criteria->compare('subscriber_id', $answer['subscriber_id']);
                    $criteria->addCondition('(status = 6 or status = 3)');
                    $checkAnser = Answer::model()->count($criteria);
                    Yii::log("\n Tong so cau tra loi dung: " . count($checkAnser));
                    if($checkAnser > 5){
                        if($teacher->point > 0 && $teacher->point <=20){
                            $teacher->fcoin += ($level->fcoin*20)/100;
                        }else
                        if($teacher->point > 20 && $teacher->point <=100){
                            $teacher->fcoin += ($level->fcoin*25)/100;
                        }else
                        if($teacher->point > 100){
                            $teacher->fcoin += ($level->fcoin*30)/100;
                        }else{
                            $teacher->fcoin +=$level->fcoin;
                        }
                    }
                    $teacher->point +=$level->point_plus;
                    $teacher->save();
                }else if($params['status'] == 2){
                    $question = Question::model()->findByPk($question_id_ck);
                    $question->status = 5;
                    $question->save();
                    $answer->status = 5;
                    $status = 2;
                }  else {
                    echo json_encode(array('code' => 1, 'message' => 'status 1 or 2'));
                    return;
                }
                $answer_check = new AnswerCheck();
                $answer_check->answer_id = $answer['id'];
                $answer_check->subscriber_id = $params['user_id'];
                $answer_check->type = $params['type'];
                $answer_check->status = $params['status'];
                $answer_check->count_check = 1;
                $answer_check->create_date = date('Y-m-d H:i:s');
                if(!$answer_check->save()){
                    echo json_encode(array('code' => 1, 'message' => 'can not update status'));
                    return;
                }
                if(!$answer->save()){
                    echo json_encode(array('code' => 1, 'message' => 'can not update status'));
                    return;
                }
                $answer = array(
                    'id'=>$params['question_id'],
                    'user_id'=>$params['user_id'],
                    'status'=>$status,
                );
                echo json_encode(array('code' => 0, 'message' => 'Update successfully', 'item'=>$answer));
                return;
            }
            else{
                echo json_encode(array('code' => 1, 'message' => 'type 1'));
                return;
            }
        }else{
            $status = ($question->status != 3)? 4: 3;
            $answer_check = new AnswerCheck();
            $answer_check->answer_id = $answer['id'];
            $answer_check->subscriber_id = $params['user_id'];
            $answer_check->type = $params['type'];
            $answer_check->status = $params['status'];
            $answer_check->count_check = 4;
            $answer_check->create_date = date('Y-m-d H:i:s');
            $answer_check->save();
            $answers = array(
                    'id'=>$params['question_id'],
                    'status'=>$status,
                    'user_id'=>$params['user_id'],
                    'count_check'=>4,
                );
            echo json_encode(array('code' => 0, 'message' => 'Update successfully', 'item'=>$answers));
                return;
        }
    }
}
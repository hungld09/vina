<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 23/11/15
 * Time: 10:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ReportAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_GET;
        $questionId = isset($params['question_id']) ? $params['question_id'] : null;
        $subscriberId = isset($params['user_id']) ? $params['user_id'] : null;
        $type = isset($params['type']) ? $params['type'] : 1;
        // type -> 1: an, 2: sex, 3: sai pham, 4: spam, Default 1
        if ($questionId == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params question_id'));
            return;
        }
        if ($subscriberId == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
            return;
        }
        if (!Question::model()->exists('id = '. $questionId)){
            echo json_encode(array('code' => 5, 'message' => 'Question is not exist'));
            return;
        }
//        $question = Question::model()->findByPk($questionId);
        $report = new ReportQuestion;
        $report->subscriber_id= $subscriberId;
        $report->question_id= $questionId;
        $report->created_date= date('Y-m-d H:i:s');
//        if($type == 1){
//            $report->type= $type;
//            $question->status = 9;
//        } else 
        if($type == 2){
            $report->type= $type;
//            $question->status = 10;
        } else if($type == 3){
            $report->type= $type;
//            $question->status = 11;
        } else if($type == 4){
            $report->type= $type;
//            $question->status = 12;
        }else{
            $report->type= $type;
//            $question->status = 9;
        }
        if(!$report->save()){
            echo json_encode(array('code' => 7, 'message' => 'Server errors'));
            return;
        }
//        if(!$question->save()){
//            echo json_encode(array('code' => 7, 'message' => 'Server errors'));
//            return;
//        }
//        $notifiQuestion = NotifiQuestion::model()->findByAttributes(array('class_id'=>$question->class_id, 'subject_id'=>$question->category_id));
//        if(count($notifiQuestion) > 0  && $notifiQuestion->count > 0){
//            $notifiQuestion->count -= 1;
//            $notifiQuestion->save();
//        }
//        $result = ReportQuestion::model()->findAllByAttributes(array('subscriber_id'=>$question->subscriber_id));
//        if(count($result) >= 3){
//            $subscriber = Subscriber::model()->findByPk($question->subscriber_id);
//            $subscriber->status = 5;//status = 3 bi report
//            $subscriber->save();
//        }
        echo json_encode(array('code' => 0, 'message'=>'Report success')); return;
    }
}
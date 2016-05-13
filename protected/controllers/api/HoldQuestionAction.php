<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dangtd
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 *///

class HoldQuestionAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $params = $_POST;
        $questionId = isset($params['question_id']) ? $params['question_id'] : null;
        $type = isset($params['type']) ? $params['type'] : null;
        $user_id = isset($params['user_id']) ? $params['user_id'] : null;
        if ($questionId == null || $type == null) {
            if ($questionId == null) {
                echo json_encode(array('code' => 5, 'message' => 'Missing params question_id'));
            }
            if ($type == null) {
                echo json_encode(array('code' => 5, 'message' => 'Missing params type'));
            }
            return;
        }
        $time2 = 0;
//        echo date('H:i:s',time()).' | ';
//        echo date('H:i:s',time() + 30 * 60);die;
        if ($type == 2) {
            $time = time();
            $holdQuestion = HoldQuestion::model()->findByAttributes(array('question_id'=>$questionId, 'subscriber_id'=>$user_id), "end_time > $time");
            $checkanswer = Answer::model()->findByAttributes(array('question_id'=>$questionId));
            if($checkanswer != null){
                $holdQuestion->status = 1;
            }else{
                $holdQuestion->status = 0;
            }
            $holdQuestion->end_time = $time;
            $holdQuestion->save();
        }
        $time = time();
        $criteria= new CDbCriteria;
        $criteria->condition = "end_time > $time";
        $criteria->compare('question_id',$questionId);
        $criteria->order = 'id DESC';
        $arrHoldQuestion = HoldQuestion::model()->findAll($criteria);
        if(count($arrHoldQuestion) > 0){
            $check = 'yes';
            $subId = $arrHoldQuestion[0]['subscriber_id'];
            $time2 = $arrHoldQuestion[0]['end_time'] - time();
        }else{
            if ($type == 1) {
                $checkanswer = Answer::model()->findByAttributes(array('question_id'=>$params['question_id'], 'status'=>2));
                if($checkanswer != null){
                    echo json_encode(array('code' => 5, 'message' => 'Câu hỏi đã có câu trả lời')); return;
                }
                $criteria= new CDbCriteria;
                $criteria->condition = "end_time > $time";
                $criteria->compare('subscriber_id',$user_id);
                $arrHoldsubs = HoldQuestion::model()->findAll($criteria);
                if(count($arrHoldsubs) > 0){
                    echo json_encode(array('code' => 5, 'message' => 'Bạn đang ghim câu hỏi khác rồi')); return;
                }
                $question = Question::model()->findByPk($questionId);
                $level = Level::model()->findByPk($question->level_id);
                $moreTime = $level->time*60;
                $time2 = $moreTime;
                $holdQuestion = new HoldQuestion();
                $holdQuestion->question_id = $questionId;
                $holdQuestion->subscriber_id = $user_id;
                $holdQuestion->start_time = time();
                $holdQuestion->end_time = time() + $moreTime;
                if(!$holdQuestion->save()){
                    echo '<pre>';var_dump($holdQuestion->getErrors());
                }
                $subId = $user_id;
                $check = 'yes';
            }else{
                $subId = '';
                $check = 'no';
            }
        }
//        $arrQuestion = array();
//        foreach($arrHoldQuestion as $item){
//            $arrQuestion[] = $item->question_id;
//        }

//        echo json_encode($check);
//        return;
        echo json_encode(array('code' => 0, 'subscriber_id' => $subId, 'check' => $check, 'time' => $time2));
        return;
    }
}
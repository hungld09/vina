<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 4/12/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ChangeLevelAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        $questionId = isset($params['question_id']) ? $params['question_id'] : null;
        $subscriberId = isset($params['user_id']) ? $params['user_id'] : null;
        $levelId = isset($params['level_id_old']) ? $params['level_id_old'] : null;
        if ($questionId == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params question_id'));
            return;
        }
        if ($subscriberId == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
            return;
        }
        if ($levelId == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params level'));
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
        if (!Level::model()->exists('id = '. $levelId)){
            echo json_encode(array('code' => 5, 'message' => 'Level is not exist'));
            return;
        }
        $levelId_new = $levelId+1;
        if (!Level::model()->exists('id = '. $levelId_new)){
            echo json_encode(array('code' => 5, 'message' => 'Mức độ hiện tại là cao nhất.'));
            return;
        }
        $time = time();
        $Changelevel = ChangeLevel::model()->findByAttributes(array('level_id'=>$levelId, 'question_id'=>$questionId, 'status'=>1));
        if($Changelevel != null){
            echo json_encode(array('code' => 5, 'message' => 'Đã có người đề xuất tăng mức độ'));
            return;
        }
        $checkHold = HoldQuestion::model()->findByAttributes(array('question_id'=>$questionId), "end_time > '$time' ");
        if($checkHold != null){
            echo json_encode(array('code' => 5, 'message' => 'Đã có người ghim câu hỏi.'));
            return;
        }
        $Changelevel = new ChangeLevel();
        $Changelevel->subscriber_id= $subscriberId;
        $Changelevel->question_id= $questionId;
        $Changelevel->level_id= $levelId;
        $Changelevel->created_date= date('Y-m-d H:i:s');
        $Changelevel->save();

        $question = Question::model()->findByPk($questionId);
        $sub_question = Subscriber::model()->findByPk($question->subscriber_id);
        $notification = new Subscriber();
        $content = 'Câu hỏi của bạn vượt mức độ, bạn có muốn tăng mức độ không';
        if (count($sub_question) > 0) {
            if ($sub_question->device_token != null || $sub_question->device_token != '') {
                $registatoin_ids = array($sub_question->device_token);
                if ($sub_question->device_type == 1) {
                    $message = $content;
                    foreach ($registatoin_ids as $deviceToken) {
                        $notification->ios_notification($deviceToken, $message);
                    }
                }
                if ($sub_question->device_type == 2) {
                    $message = array(
                        'Title' => 'Học dễ',
                        "Notice" => $content,
                        'Type' => 5,
                        'QuestionId' => $questionId,
                    );
                    $notification->send_notification($registatoin_ids, $message);
                }
            }
        }

        echo json_encode(array('code' => 0, 'statusLevel'=> 1 ,'message' => 'Đề xuất tăng mức độ thành công, Bạn đợi học sinh xác nhận.')); return;
    }
}
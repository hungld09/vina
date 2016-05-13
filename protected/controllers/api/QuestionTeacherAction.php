<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 12/12/15
 * Time: 10:18 PM
 * To change this template use File | Settings | File Templates.
 */

class QuestionTeacherAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        $connection = Yii::app()->db3;
        if (!isset($params['user_id'])){
            echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
                return;
        }
        if (!isset($params['class_id'])){
            echo json_encode(array('code' => 1, 'message' => 'Missing params class_id'));
                return;
        }
        if (!isset($params['subject_id'])){
            echo json_encode(array('code' => 1, 'message' => 'Missing params subject_id'));
                return;
        }
        if (!Subscriber::model()->exists('id = '. $params['user_id'])){
            echo json_encode(array('code' => 1, 'message' => 'Subscriber is not exist'));
            return;
        }
        $checkTest = SubscriberCheckTest::model()->findByAttributes(array(
            'class_id' => $params['class_id'],
            'subscriber_id' => $params['user_id'],
            'subject_id' => $params['subject_id'],
        ), 'point >= 18');
        if($checkTest != null){
            echo json_encode(array('code' => 5, 'message' => 'Bạn đã làm bài kiểm tra năng lực này.'));
            return;
        }
        $checkTest = SubscriberCheckTest::model()->findByAttributes(array(
            'class_id' => $params['class_id'],
            'subscriber_id' => $params['user_id'],
            'subject_id' => $params['subject_id'],
        ));
        if($checkTest != null){
//            if($checkTest->times >=3){
//                echo json_encode(array('code' => 5, 'message' => 'Bạn đã hết quyền làm bài thi môn này.'));
//                return;
//            }
            $checkTest->times += 1;
            $checkTest->start_time = time();
            $checkTest->end_time = time()+30*60;
        }else{
            $checkTest = new SubscriberCheckTest;
            $checkTest->class_id = $params['class_id'];
            $checkTest->subscriber_id = $params['user_id'];
            $checkTest->subject_id = $params['subject_id'];
            $checkTest->times = 1;
            $checkTest->create_date = date('Y-m-d H:i:s');
            $checkTest->start_time = time();
            $checkTest->end_time = time()+30*60;
        }
        $checkTest->save();
        $exam_easy = array();
        $exam_normal = array();
        $exam_hard = array();
        $classId = $params['class_id'];
        $subjectId = $params['subject_id'];
        $uid = $params['user_id'];
        $query = "select * from question_import where subject_id = $subjectId and level = 1 and class_id = $classId";
        $command = $connection->createCommand($query);
        $question_easy = $command->queryAll();
//        $question_easy = QuestionImport::model()->findAllByAttributes(array('level'=>1, 'class_id'=>$params['class_id'], 'subject_id'=>$params['subject_id']));
        if(count($question_easy) <= 5){
            echo json_encode(array('code' => 5, 'message' => 'Chưa cập nhập đề thi.'));
                return;
        }
        $query = "select * from question_import where subject_id = $subjectId and level = 2 and class_id = $classId";
        $command = $connection->createCommand($query);
        $question_normal = $command->queryAll();
//        $question_normal = QuestionImport::model()->findAllByAttributes(array('level'=>2, 'class_id'=>$params['class_id'], 'subject_id'=>$params['subject_id']));
        if(count($question_normal) <= 5){
            echo json_encode(array('code' => 5, 'message' => 'Chưa cập nhập đề thi.'));
                return;
        }
        $query = "select * from question_import where subject_id = $subjectId and level = 3 and class_id = $classId";
        $command = $connection->createCommand($query);
        $question_hard = $command->queryAll();
//        $question_hard = QuestionImport::model()->findAllByAttributes(array('level'=>3, 'class_id'=>$params['class_id'], 'subject_id'=>$params['subject_id']));
        if(count($question_hard) <= 5){
            echo json_encode(array('code' => 5, 'message' => 'Chưa cập nhập đề thi.'));
                return;
        }
        $question_easy_rand = array_rand($question_easy, 5);
        for ($i = 0; $i < count($question_easy_rand); $i++){
            array_push($exam_easy, $question_easy[$question_easy_rand[$i]]);
        }
        $question_normal_rand = array_rand($question_normal, 5);
        for ($j = 0; $j < count($question_normal_rand); $j ++){
            array_push($exam_easy, $question_normal[$question_normal_rand[$j]]);
        }
        $question_hard_rand = array_rand($question_hard, 5);
        for ($k= 0; $k < count($question_hard_rand); $k ++){
            array_push($exam_easy, $question_hard[$question_hard_rand[$k]]);
        }
        shuffle($exam_easy);
        echo json_encode(array('code' => 0, 'Questions'=>$exam_easy, 'time'=>'30'));
            return;
    }
}


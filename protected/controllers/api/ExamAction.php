<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 17/12/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ExamAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_GET;
        if (!isset($params['user_id'])) {
            echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
            return;
        }
        if (!isset($params['class_id'])) {
            echo json_encode(array('code' => 1, 'message' => 'Missing params class_id'));
            return;
        }
        $query = "select *, sct.subscriber_id, sct.point, sct.subject_id, sct.class_id from subject_category sc join subscriber_check_test sct on sc.id = sct.subject_id where sc.type = 1 and sct.class_id = ".$params['class_id']." and sct.subscriber_id = ".$params['user_id']." and sct.point >= 18";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $subject = $command->queryAll();
//        for ($j= 0; $j < count($subject); $j++){
//            $subject['point'] = $subject[$j]['point'];
//        }
        echo json_encode(array('code'=> 0 , 'Subject'=> array('title'=>'Lớp học', 'items'=>$subject)));
        return;
    }
}
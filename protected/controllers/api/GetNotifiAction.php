<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 24/10/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class GetNotifiAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $query = 'select id, class_name from class';
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $class = $command->queryAll();
        $arrClass = array();
        for ($i = 0; $i < count($class); $i ++){
            $id = $class[$i]['id'];
            $arrClass[$i]['class_id'] = $id;
            $arrClass[$i]['class_name'] = $class[$i]['class_name'];
            $query = "SELECT * FROM notifi_question where class_id = $id";
            $arrClass[$i]['category'] = Yii::app()->db->createCommand($query)->queryAll();
        }
//        echo '<pre>'; print_r($notifiQuestion);
        echo json_encode(array('code' => 0, 'items'=>$arrClass));
    }
}


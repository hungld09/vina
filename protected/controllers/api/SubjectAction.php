<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class subjectAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $query = 'select * from subject_category where status = 1 and type = 1';
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $subject = $command->queryAll();
//        echo count($subject);die;
        echo json_encode(array('code'=> 0,'Subject'=> array('title'=>'Môn học', 'items'=>$subject)));
        return;
    }
}
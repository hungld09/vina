<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ClassAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $type = isset($_POST['type']) ? $_POST['type'] : 1;
        $query = 'select * from class where status = 1';
//        $class= Class1::model()->findAllBySql($query);
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $class = $command->queryAll();
        for ($i= 0; $i<count($class); $i++){
            $subject = SubjectCategory::model()->findAllByAttributes(array('class_id'=>$class[$i]['id'], 'type'=>$type));
            for ($j= 0; $j < count($subject); $j++){
                $class[$i]['subject'][$j]['id'] = $subject[$j]['id'];
                $class[$i]['subject'][$j]['subject_name'] = $subject[$j]['subject_name'];
                $class[$i]['subject'][$j]['create_date'] = $subject[$j]['create_date'];
                $class[$i]['subject'][$j]['description'] = $subject[$j]['description'];
            }
        }
        echo json_encode(array('code'=> 0 , 'Class'=> array('title'=>'Lớp học', 'items'=>$class)));
        return;
    }
}
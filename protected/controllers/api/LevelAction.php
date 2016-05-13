<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 09/11/15
 * Time: 10:00 PM
 * To change this template use File | Settings | File Templates.
 */

class LevelAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $query = 'select * from level';
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $level = $command->queryAll();
        echo json_encode(array('code'=> 0, 'Level'=> array('title'=> 'Level','items'=>$level)));
    }
}
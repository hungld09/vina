<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 24/10/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class PartnerAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $query = 'select * from partner where status = 1';
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $partner = $command->queryAll();
        echo json_encode(array('code'=> 0, 'Partner'=> array('title'=> 'Đối tác','items'=>$partner)));
    }
}
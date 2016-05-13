<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class Test2Action extends CAction{
    public function run(){
        header('Content-type: application/json');
        $bool = true;
        echo json_encode(array('test'=> $bool));
        return;
    }
}
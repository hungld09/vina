<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class TestAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $bool = false;
        echo json_encode(array('test'=> $bool));
        return;
    }
}
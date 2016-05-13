<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ListserviceAction extends CAction{
    public function run(){
        header('Content-type: application/json');

//        $page = isset($_GET['page_number']) ? $_GET['page_number'] : 1;
//        $limit = isset($_GET['page_size']) ? $_GET['page_size'] : 20;
//        $offset = ($page - 1) * $limit;

        $arrService = Service::model()->findAllByAttributes(
            array(
                'status'=>1,
                'type'=>1,
            ),
            array(
                'order'=>'id asc',
//                'limit' => $limit,
//                'offset' => $offset
            )
        );

        $service = array();
        for ($i = 0; $i < count($arrService); $i++){
            $service[$i]['id'] = $arrService[$i]['id'];
            $service[$i]['display_name'] = $arrService[$i]['display_name'];
            $service[$i]['description'] = $arrService[$i]['description'];
            $service[$i]['price'] = $arrService[$i]['price'];
//            $service[$i]['type'] = $arrService[$i]['default'];
        }
        echo json_encode(array('code' => 0,'Service' => array('title' => 'Danh sách service','items'=>$service)));
    }   
}
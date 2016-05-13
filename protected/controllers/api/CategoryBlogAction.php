<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class CategoryBlogAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $arrCategory = CategoryBlog::model()->findAll();
            if(count($arrCategory) == 0){
                echo json_encode(array('code' => 5, 'message' => 'Không có dữ liệu'));
                return;
            }
            $content = array();
            for ($j= 0; $j < count($arrCategory); $j++){
                $content[$j]['id'] = $arrCategory[$j]['id'];
                $content[$j]['title'] = $arrCategory[$j]['title'];
            }
            echo json_encode(array('code'=> 0 , 'items' => $content));
            return;
    }
}
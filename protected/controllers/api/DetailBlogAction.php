<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dangtd
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 *///
class DetailBlogAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $params = $_POST;
        $id = isset($params['id']) ? $params['id'] : 0;
        if($id == 0){
            echo json_encode(array('code' => 5, 'message' => 'Missing params id'));
            return;
        }
        $content = array();
        $Blog = Blog::model()->findByPk($id);
        $content['title'] = '<h3>'. $Blog['title'] .'</h3>';
        $content['content'] = $Blog['content'];
        echo json_encode(array('code'=> 0 , 'items'=> $content));
        return;
    }
}
?>

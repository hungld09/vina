<?php

/**
 * Created by JetBrains PhpStorm.
 * User: dangtd
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */
class BannerAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $params = $_POST;
        $type = isset($params['type']) ? $params['type'] : '';
        if ($type == '') {
            echo json_encode(array('code' => 1, 'message' => 'Missing params type'));
            return;
        }
        $banner = Banner::model()->findByAttributes(array('status'=>1, 'type'=>0));
        if(count($banner) > 0){
            $image_url = IPSERVER.'web/banner/'.$banner['image_url'];
            $link = $banner['link'];
        }else{
            $image_url = '';
            $link = '';
        }

        echo json_encode(array('code' => 0, 'item'=>array('link' => $link, 'image_url' => $image_url)));
        return;
    }
}
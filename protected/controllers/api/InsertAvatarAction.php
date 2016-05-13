<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 *///

class InsertAvatarAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        $subscriberId = isset($params['subscriber_id']) ? $params['subscriber_id'] : null;
        $extension = isset($params['extension']) ? $params['extension'] : null;
        $image_base64 = isset($params['image_base64']) ? $params['image_base64'] : null;
        if($subscriberId == null || $extension == null || $image_base64 == null){
            if($subscriberId == null){
                echo json_encode(array('code' => 5, 'message' => 'Missing params subscriber_id'));
            }
            if($extension == null){
                echo json_encode(array('code' => 5, 'message' => 'Missing params extension'));
            }
            if($image_base64 == null){
                echo json_encode(array('code' => 5, 'message' => 'Missing params image_base64'));
            }
            return;
        }

        $director = '/var/www/html/web/uploadavatar/';
        if (!file_exists($director)) {
            mkdir($director, 0777, true);
        }

        $binary = base64_decode($image_base64);
        header('Content-Type: bitmap; charset=utf-8');

        $file_name = date('YmdHis') . '-' . rand() . '.' . $extension;
        $path = fopen($director . '/' . $file_name, 'w+');

        //write file
        fwrite($path, $binary);
        //close file stream
        fclose($path);
        
        $subscriber = Subscriber::model()->findByPk($subscriberId);
        $subscriber->url_avatar = 'web/uploadavatar/' . $file_name;
        if($subscriber->save()){
            header('Content-type: application/json');
            $avata = IPSERVER.$subscriber->url_avatar;
            echo json_encode(array('code' => 0, 'message' => 'Upload avatar successfully', 'url_avatar'=>$avata));
            return;
        }else{
            header('Content-type: application/json');
            echo json_encode(array('code' => 5, 'message' => 'Upload avatar failed'));
            return;
        }
    }
}
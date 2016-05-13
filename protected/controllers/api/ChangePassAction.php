<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ChangePassAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        if(!isset($params['user_id']) || !isset($params['password']) || !isset($params['password_new'])){
            if(!isset($params['user_id'])){
                echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
            }
            if(!isset($params['password'])){
                echo json_encode(array('code' => 1, 'message' => 'Missing params password'));
            }
            if(!isset($params['password_new'])){
                echo json_encode(array('code' => 1, 'message' => 'Missing params password_new'));
            }
            return;
        }
        if (!Subscriber::model()->exists('id = '. $params['user_id'])){
            echo json_encode(array('code' => 1, 'message' => 'user_id is not exist'));
            return;
        }
        $pass = MD5($params['password']).'_echat';
        $pass_new = MD5($params['password_new']).'_echat';
        $user = Subscriber::model()->findByAttributes(array('id'=>$params['user_id'], 'password'=>$pass));
        if(count($user) > 0){
            $user->password = $pass_new;
            if($user->save()){
                echo json_encode(array('code' => 0, 'message' => 'Change Password successfully'));
                return;
            }
        }else{
            echo json_encode(array('code' => 5, 'message' => 'Password cu khong dung'));
            return;
        }
        
    }
}
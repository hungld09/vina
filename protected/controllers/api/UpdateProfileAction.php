<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class UpdateProfileAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        if(!isset($params['user_id'])){
            if(!isset($params['user_id'])){
                echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
                return;
            }
        }
        if (!Subscriber::model()->exists('id = '. $params['user_id'])){
            echo json_encode(array('code' => 1, 'message' => 'user_id is not exist'));
            return;
        }
        $subs = Subscriber::model()->findByPk($params['user_id']);
        if(isset($params['phone_number'])){
           $subs->phone_number = $params['phone_number'];
        }
        if(isset($params['lastname'])){
           $subs->lastname = $params['lastname'];
        }
        if(isset($params['firstname'])){
           $subs->firstname = $params['firstname'];
        }
        if(isset($params['email'])){
           $subs->email = $params['email'];
        }
        if(isset($params['class_id'])){
           $subs->class_id = $params['class_id'];
        }
        if(!$subs->save()){
           echo json_encode(array('code' => 1, 'message' => 'Cannot update profile'));
            return; 
        }else{
//            $subs = (array)$subs;
            if($subs->url_avatar != null){
                if($subs->password == 'faccebook' || $subs->password == 'Google'){
                    $url_avatar = $subs->url_avatar;
                }else{
                    $url_avatar = IPSERVER . $subs->url_avatar;
                }
//                $url_avatar = IPSERVER.$subs->url_avatar;
            }else{
                $url_avatar = '';
            }
            $lastname = !empty($subs->lastname) ? $subs->lastname : '';
            $firstname = !empty($subs->firstname) ? $subs->firstname : '';
            $name = $firstname . ' ' . $lastname;
            $mail = !empty($subs->email) ? $subs->email : "";
            $usingService = ServiceSubscriberMapping::model()->findByAttributes(
                array(
                    'subscriber_id' => $subs->id,
                    'is_active' => 1
                )
            );
           echo json_encode(array(
               'code' => 0,
               'item'=>array(
                   'id'=>$subs->id,
                   'username'=> !empty($name) ? $name : 'Noname',
                   'url_avatar'=> $url_avatar,
                   'phone_number'=> !empty($subs->phone_number) ? (string)$subs->phone_number : '',
                   'lastname'=> !empty($subs->lastname) ? $subs->lastname : '',
                   'firstname'=> !empty($subs->firstname) ? $subs->firstname : '',
                   'fcoin'=> !empty($subs->fcoin) ? $subs->fcoin : '',
                   'type'=> !empty($subs->type) ? $subs->type : '',
                   'service_id' => !empty($usingService['service_id']) ? $usingService['service_id'] : 0,
                   'email' => $mail,
                   'service_expiry_date' => !empty($usingService['expiry_date']) ? date('Y-m-d', strtotime($usingService['expiry_date'])) : ''
               ),
           ));
            return;  
        }
    }
}
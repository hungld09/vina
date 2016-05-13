<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class LoginAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        if (!isset($params['username']) || !isset($params['password'])){
           if(!isset($params['username'])){
               echo json_encode(array('code' => 5, 'message' => 'Missing params username'));
           } 
           if(!isset($params['password'])){
               echo json_encode(array('code' => 5, 'message' => 'Missing params password'));
           } 
           return;
        }
        $username = Subscriber::model()->findByAttributes(
            array(
                'username'=>$params['username'],
            )
        );
        if($username != null && $username->status == 5){
            echo json_encode(array('code' => 7, 'message' => 'Tài khoản của bạn đang tạm khóa, xin vui lòng gọi đường dây nóng (04 4450 8388) để biết thêm chi tiết'));
            return;
        }
        if($username != null && $username->status == 2){
            echo json_encode(array('code' => 20, 'message' => 'Tài khoản của bạn chưa xác nhận email'));
            return;
        }
        if($username == null){
            echo json_encode(array('code' => 21, 'message' => 'Tên đăng nhập không chính xác'));
            return;
        }
        $pass = MD5($params['password']).'_echat';
        if($username->password == $pass){
            $fullSubs = Subscriber::model()->findByPk($username->id);
            //sessionkey khi dang nhap success
            $sessionKey = CUtils::generateSessionKey($fullSubs->id);
            if($fullSubs->url_avatar != null){
                $url_avatar = IPSERVER.$fullSubs->url_avatar;
            }else{
                $url_avatar = '';
            }
            $lastname = !empty($fullSubs->lastname) ? $fullSubs->lastname : '';
            $firstname = !empty($fullSubs->firstname) ? $fullSubs->firstname : '';
            $name = $firstname . ' ' . $lastname;
            $version = Version::model()->findByPk(2);
            $usingService = ServiceSubscriberMapping::model()->findByAttributes(
                array(
                    'subscriber_id' => $fullSubs->id,
                    'is_active' => 1
                )
            );
            $statusExam = '';
            if($fullSubs->type == 2){
                if($fullSubs->status == 1){
                    $statusExam = 1; //da lam bai kiem tra
                }else{
                    $statusExam = 2;// chua lam bai kiem tra
                }
            }
            if($username != null && $username->status == 3){
                $code = 10;
                $message = 'Bạn chưa làm bài kiểm tra giáo viên';
            }else if($username != null && $username->status == 4){
                $code = 11;
                $message = 'Bạn chưa làm bài kiểm tra hệ thống';
            }else{
                $code = 0;
                $message = 'Bạn đăng nhập thành công';
            }
            $subFree = PromotionFreeContent::model()->findByAttributes(array('subscriber_id' => $fullSubs->id));
            if($fullSubs['partner_id'] == 'net2e' && $subFree != null){
                $dem = 2-$subFree['total'];
            }else if($subFree != null){
                $dem = 5-$subFree['total'];
            }else{
                $dem= 0;
            }
            echo json_encode(array(
	                'code'=>$code,
            		'sessionkey'=>$sessionKey,
            		'message'=>$message,
	                'item'=>array(
	                    'id'=>$fullSubs->id,
	                    'username'=> !empty($name) ? $name : 'Noname',
	                    'url_avatar'=> $url_avatar,
                            'user_name'=> !empty($fullSubs->username) ? (string)$fullSubs->username : '',
	                    'phone_number'=> !empty($fullSubs->phone_number) ? (string)$fullSubs->phone_number : '',
	                    'lastname'=> !empty($fullSubs->lastname) ? $fullSubs->lastname : '',
	                    'firstname'=> !empty($fullSubs->firstname) ? $fullSubs->firstname : '',
	                    'fcoin'=> !empty($fullSubs->fcoin) ? $fullSubs->fcoin : '',
	                    'point'=> !empty($fullSubs->point) ? $fullSubs->point : '',
	                    'type'=> !empty($fullSubs->type) ? $fullSubs->type : '',
	                    'statusExam'=> $statusExam,
	                    'email'=> !empty($fullSubs->email) ? $fullSubs->email : '',
	                    'version'=> !empty($version->version) ? $version->version : '0.0',
                            'totalFree'=> $dem,
	                    'versionApp'=> '1.0',
	                    'currency'=> 'coin',
                        'service_id' => !empty($usingService['service_id']) ? $usingService['service_id'] : 0,
                        'service_expiry_date' => !empty($usingService['expiry_date']) ? date('Y-m-d', strtotime($usingService['expiry_date'])) : '',
                        ),
            ));
        }else {
            echo json_encode(array('code' => 5, 'message' => 'Mật khẩu không chính xác'));
            return;
        }
    }
}

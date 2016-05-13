<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 21/12/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class TypeAccountAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        $subscriberId = isset($params['user_id']) ? $params['user_id'] : null;
        $email = isset($params['email']) ? $params['email'] : null;
        $type = isset($params['type']) ? $params['type'] : 1; //1: Hoc Sinh, 2: Thay giao
        Yii::log("-------------------------------------------------TypeAccountAction:------------------------------------");
        if ($subscriberId == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params question_id'));
            return;
        }
        Yii::log("--type:--------$type---------");
        $subscriber = Subscriber::model()->findByPk($subscriberId);
        if($subscriber == null){
            echo json_encode(array('code' => 5, 'message' => 'Subscriber is not exist'));
            return;
        }
        $subscriber->type = $type;
        $subscriber->save();
        if($subscriber->url_avatar != null){
            $url_avatar = $subscriber->url_avatar;
        }else{
            $url_avatar = '';
        }
        Yii::log("--id:--------$subscriber->id---------");
        Yii::log("--id:--------$email---------");
        $version = Version::model()->findByPk(2);
            $usingService = ServiceSubscriberMapping::model()->findByAttributes(
                array(
                    'subscriber_id' => $subscriber->id,
                    'is_active' => 1
                )
            );
        if($type == 1){
            $checkUser = $this->TypeHS($subscriber, $type, $email);
        }else if($email != null){
            $checkUser = $this->TypeGV($subscriber, $type, $email);
        }
        if($checkUser->status == 3){
            $message = 'Bạn chưa làm bài kiểm tra chuyên môn';
            $code = 10;
        }else if($checkUser->status == 4){
            $message = 'Bạn chưa làm kiểm tra hệ thống';
            $code = 11;
        }else{
            $message = 'Bạn đăng nhập thành công';
            $code = 0;
        }
        $profile = array(
                'id'=>$subscriber->primaryKey,
                'username'=> $subscriber->firstname . ' ' . $subscriber->lastname,
                'url_avatar'=> $url_avatar,
                'phone_number'=> !empty($subscriber->phone_number) ? (string)$subscriber->phone_number : '',
                'lastname'=> !empty($subscriber->lastname) ? $subscriber->lastname : '',
                'firstname'=> $subscriber->lastname,
                'fcoin'=> !empty($subscriber->fcoin) ? $subscriber->fcoin : '',
                'type'=> !empty($subscriber->type) ? $subscriber->type : '',
                'version'=> !empty($version->version) ? $version->version : '0.0',
                'versionApp'=> '1.0',
                'currency'=> 'coin',
                'service_id' => !empty($usingService['service_id']) ? $usingService['service_id'] : 0,
                'service_expiry_date' => !empty($usingService['expiry_date']) ? date('Y-m-d', strtotime($usingService['expiry_date'])) : '',
                    
            );
            echo json_encode(array('code' => $code, 'item'=>$profile, 'message' => $message));
//            Yii::app()->mail->send($message);
            return;
    }
    public function TypeHS($subscriber, $type, $email)
    {
        $subscriber->type = $type;
        $subscriber->status = 1;
        $subscriber->save();
        if($email != null || $email != ''){
                 $content = 'Xin chào '.$subscriber->username.'<br>'.'. Bạn vừa đăng ký tài khoản trên https://www.hocde.vn. Bạn cần đọc kỹ điều khoản cung cấp dịch vụ của chúng tôi tại links sau : <a href="https://www.hocde.vn/site/clause">https://www.hocde.vn/site/clause</a>
Việc bạn click vào link xác thực sau đây đồng nghĩa với việc bạn xác nhận tài khoản đã đăng ký và chấp nhận các điều khoản cung cấp dịch vụ của Học Dễ :'.'<br>';
//                $content .= '<a href="https://www.hocde.vn/site/confirmRegister?sessionkey='.$session->token.'">https://www.hocde.vn/site/confirmRegister?sessionkey='.$sessionKey.'</a></br>Cám ơn bạn đã sử dụng dịch vụ của chúng tôi!';
//                try {
                    $message = new YiiMailMessage;
                    $message->setBody($content, 'text/html');
                    $message->subject = "[HocDe] Thông báo";
                    $message->addTo($email);
//                    $message->from = 'cskh.socotec04@gmail.com';
//                    Yii::app()->mail->send($message); 
//                } catch (Exception $ex) {
//                    echo json_encode(array('code' => 40, 'item'=>'Quá trình gửi mail sảy ra lỗi, bạn vui long kiểm tra lai mail.'));
//                }
            }
            return $subscriber;
    }
    public function TypeGV($subscriber, $type, $email)
    {
        $subscriber->type = $type;
        $subscriber->status = 4;
        $subscriber->save();
        $session = AuthToken::model()->findByPk($subscriber->id);
        $confirmRegister = new ConfirmRegister();
        $confirmRegister->subscriber_id = $subscriber->id;
        $confirmRegister->sessionkey = $session->token;
        $confirmRegister->end_time = time() + 24*60*60;
        if($confirmRegister->save()){
            if($email != null || $email != ''){
//                try {
                    $content = 'Xin chào ' . $subscriber->username . '<br>' . '. Bạn vừa đăng ký tài khoản trên https://www.hocde.vn. Bạn cần đọc kỹ điều khoản cung cấp dịch vụ của chúng tôi tại links sau : <a href="https://www.hocde.vn/site/clause">https://www.hocde.vn/site/clause</a>
Việc bạn click vào link xác thực sau đây đồng nghĩa với việc bạn xác nhận tài khoản đã đăng ký và chấp nhận các điều khoản cung cấp dịch vụ của Học Dễ :' . '<br>';
                    $content .= '<a href="https://www.hocde.vn/site/confirmRegister?sessionkey=' . $session->token . '">https://www.hocde.vn/site/confirmRegister?sessionkey=' . $session->token . '</a></br>Cám ơn bạn đã sử dụng dịch vụ của chúng tôi!';
                    $message = new YiiMailMessage;
                    $message->setBody($content, 'text/html');
                    $message->subject = "[HocDe] Thông báo";
                    $message->addTo($email);
//                $message->from = 'cskh.socotec04@gmail.com';
//                Yii::app()->mail->send($message); 
//                } catch (Exception $ex) {
//                    echo json_encode(array('code' => 40, 'item'=>'Quá trình gửi mail sảy ra lỗi, bạn vui long kiểm tra lai mail.'));
//                }
            }
        }
        return $subscriber;
    }
}
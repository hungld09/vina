<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class RegisterAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $lastName = isset($_GET['lastname']) ? $_GET['lastname'] : null;
        $FirstName = isset($_GET['firstname']) ? $_GET['firstname'] : null;
        $Username = isset($_GET['username']) ? $_GET['username'] : null;
        $passWord = isset($_GET['password']) ? $_GET['password'] : null;
        $mobile = isset($_GET['phone_number']) ? $_GET['phone_number'] : null;
        $email = isset($_GET['email']) ? $_GET['email'] : null;
        $type = isset($_GET['type']) ? $_GET['type'] : 1; //1: Hoc Sinh, 2: Thay giao
//        $partnerID = isset($_GET['partnerId']) ? $_GET['partnerId'] : 'net2e';

        $partnerID = 'VIETS';
//        if(trim(strtolower($partnerID)) == '' || empty($partnerID)){
//            $partnerID = 'net2e';
//        }
        Yii::log("--Register3:--------$partnerID---------");
        if($Username == null){
            echo json_encode(array('code' => 5, 'message' => 'Missing params username'));
        }
        if($passWord == null){
            echo json_encode(array('code' => 5, 'message' => 'Missing params password'));
        }
         if($email == null){
             echo json_encode(array('code' => 5, 'message' => 'Missing params email'));
         }
         if(strlen($passWord) < 6 || strlen($passWord) > 12){
            echo json_encode(array('code' => 5, 'message' => 'password 6->12'));
        }
        if(!preg_match('/^[a-zA-Z0-9]{4,15}$/', $Username))
        {
            echo json_encode(array('code' => 5, 'message' => 'The username does not match the requirements')); return;
        }
        $subscriber = Subscriber::model()->findByAttributes(array('username'=> $Username));
        if(count($subscriber) > 0){
            echo json_encode(array('code' => 5, 'message' => 'Username da ton tai'));
            return;
        }
        $subscriberEmail = Subscriber::model()->findByAttributes(array('email'=> $email));
        if($subscriberEmail != null){
            echo json_encode(array('code' => 5, 'message' => 'Email đã tồn tại'));
            return;
        }
        $pass = MD5($passWord).'_echat';
        $subscriber = new Subscriber;
        $subscriber->username = $Username;
        $subscriber->email = $email;
//        $subscriber->status = 0;
        if($type == 1){
            $subscriber->status = 1;
        }else{
            $subscriber->status = 2;
        }
        
        $subscriber->password = $pass;
        $subscriber->phone_number = $mobile;
        $subscriber->lastname = $lastName;
        $subscriber->firstname = $FirstName;
        $subscriber->partner_id = $partnerID;
        $subscriber->fcoin = 0;
        $subscriber->type = $type;// học sinh lưu type = 1 , thầy giáo = 2
        $subscriber->create_date = date('Y-m-d H:i:s');
        if($subscriber->url_avatar != null){
            $url_avatar = IPSERVER.$subscriber->url_avatar;
        }else{
            $url_avatar = '';
        }
        if($subscriber->save()){
            $sessionKey = CUtils::generateSessionKey($subscriber->primaryKey);
//            if($subscriber->partner_id == 'net2e'){
//                $total = 2;
//            }else{
//                $total = 5;
//            }
            $total = 0;
            $totalFree = 0;
            $confirmRegister = new ConfirmRegister();
            $confirmRegister->subscriber_id = $subscriber->id;
            $confirmRegister->sessionkey = $sessionKey;
            $confirmRegister->end_time = time() + 24*60*60;
            if($confirmRegister->save()){
                if($type == 1){
                    $content = 'Xin chào '.$Username.'<br>'.'Bạn vừa đăng ký tài khoản trên http://vietglobal.hocde.vn/. Bạn cần đọc kỹ điều khoản cung cấp dịch vụ của chúng tôi tại links sau : <a href="http://vietglobal.hocde.vn/site/clause">https://www.hocde.vn/site/clause</a>
    Việc bạn click vào link xác thực sau đây đồng nghĩa với việc bạn xác nhận tài khoản đã đăng ký và chấp nhận các điều khoản cung cấp dịch vụ của Học Dễ :'.'<br>'.'Bạn sẽ được hỏi '.$total.' câu miễn phí sau khi kích hoạt thành công!<br/>';
                    $content .= '<a href="http://vietglobal.hocde.vn/site/confirmRegister?sessionkey='.$sessionKey.'">http://vietglobal.hocde.vn/site/confirmRegister?sessionkey='.$sessionKey.'</a></br>Cám ơn bạn đã sử dụng dịch vụ của chúng tôi!';
                }else{
                    $content .='Xin chào '.$Username.'<br>'.'Bạn vừa đăng ký tài khoản trên http://vietglobal.hocde.vn/ <br/>Bạn cần đọc kỹ điều khoản cung cấp dịch vụ của chúng tôi tại links sau : <a href="http://vietglobal.hocde.vn/site/clause">http://vietglobal.hocde.vn/site/clause</a>
                Việc bạn click vào link xác thực sau đây đồng nghĩa với việc bạn xác nhận tài khoản đã đăng ký và chấp nhận các điều khoản cung cấp dịch vụ của Học Dễ. <br/>Mọi vấn đề thắc mắc bạn có thể tro đổi với các giáo viên online khác thông qua group https://www.facebook.com/groups/1124088407603900/ <br/> Cảm ơn bạn đã sử dụng dịch vụ chúng tôi!';
                    $content .= '<a href="http://vietglobal.hocde.vn/site/confirmRegister?sessionkey='.$sessionKey.'">http://vietglobal.hocde.vn/site/confirmRegister?sessionkey='.$sessionKey.'</a></br>Cám ơn bạn đã sử dụng dịch vụ của chúng tôi!';
                }
                $message = new YiiMailMessage;
                $message->setBody($content, 'text/html');

                $message->subject = "[HocDe] Xác nhận đăng ký của khách hàng ";
                $message->addTo($email);
//                $message->cc = array('xuanmai@socotec.vn');
//                $message->from = 'cskh.socotec02@gmail.com';
//                Yii::app()->mail->send($message);
            }
            $version = Version::model()->findByPk(2);
            $usingService = ServiceSubscriberMapping::model()->findByAttributes(
                array(
                    'subscriber_id' => $subscriber->id,
                    'is_active' => 1
                )
            );
            $profile = array(
                'id'=>$subscriber->primaryKey,
                'username'=> $FirstName . ' ' . $lastName,
                'user_name'=> $Username,
                'url_avatar'=> $url_avatar,
                'phone_number'=> !empty($subscriber->phone_number) ? (string)$subscriber->phone_number : '',
                'lastname'=> !empty($subscriber->lastname) ? $subscriber->lastname : '',
                'firstname'=> $lastName,
                'fcoin'=> !empty($subscriber->fcoin) ? $subscriber->fcoin : '',
                'type'=> !empty($subscriber->type) ? $subscriber->type : '',
                'version'=> !empty($version->version) ? $version->version : '0.0',
                'versionApp'=> '1.0',
                'currency'=> 'coin',
                'totalFree'=> $totalFree,
                'service_id' => !empty($usingService['service_id']) ? $usingService['service_id'] : 0,
                'service_expiry_date' => !empty($usingService['expiry_date']) ? date('Y-m-d', strtotime($usingService['expiry_date'])) : '',
                    
            );
            if($type == 2){
                $message = 'Tài khoản của bạn đã đăng ký thành công';
//                $message = 'Mail xác nhận đã được gửi, Xin mời bạn truy cập mail để xác minh tài khoản. Lưu ý: Tìm trong thư mục spam nếu bạn không thấy Mail xác nhận trong hộp thư đến!';
            }else{
                $message = 'Tài khoản của bạn đã đăng ký thành công';
//                $message = 'Tài khoản của bạn đã được Khởi Tạo. Để nhận thêm khuyến mại, bạn vui lòng vào mail để xác nhận.(Lưu ý kiểm tra mục Spam nếu không thấy Mail trong Hộp thư đến!)';
            }
            echo json_encode(array('code' => 0, 'sessionkey'=>$sessionKey, 'message' => $message, 'item'=>$profile));
//            Yii::app()->mail->send($message);
            return;
        }
    }
}
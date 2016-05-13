<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 23/10/15
 * Time: 10:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ResetPassAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $username = isset($_POST['username']) ? $_POST['username'] : null;
        if($username == null){
            echo json_encode(array('code' => 5, 'message' => 'Missing params username'));
        }
        $subscriber = Subscriber::model()->findByAttributes(array('username'=>$username));
        if($subscriber == null){
            echo json_encode(array('code' => 5, 'message' => 'Username Khong ton tai'));
            return;
        }
        $subscriber = Subscriber::model()->findByAttributes(array('username'=>$username, 'status'=>1));
        if($subscriber == null){
            echo json_encode(array('code' => 6, 'message' => 'Username chua xac thuc'));
            return;
        }
        if($subscriber['email'] == '' || $subscriber['email'] == null){
            echo json_encode(array('code' => 5, 'message' => 'Email Khong ton tai'));
            return;
        }
        $id = base64_encode($subscriber['id']);
        $content = 'Xin chào '.$username.'<br>'.'. Xin mời bạn bấm vào link để nhập password mới trên hocde.onedu.vn'.'<br>';
        $content .= '<a href="http://hocde.onedu.vn/site/resetPassword?id='.$id.'">http://hocde.onedu.vn/site/resetPassword?id='.$id.'</a>';

        $message = new YiiMailMessage;
        $message->setBody($content, 'text/html');

        $message->subject = "[hocde.onedu.vn] Xác nhận reset mật khẩu của khách hàng ";
        $message->addTo($subscriber['email']);
        $message->from = 'nicespace2015@gmail.com';
        Yii::app()->mail->send($message);
        
        echo json_encode(array('code' => 0, 'message' => 'Mail reset đã được gửi, Xin mời bạn truy cập mail để xác minh tài khoản'));
            return;
    }
}
    

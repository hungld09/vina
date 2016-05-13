<?php

/* 
 * Hungld
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class AnswerController extends Controller {
    public function beforeAction($action)
    {
        if (strcasecmp($action->id, 'view') == 0) {
            $sessionKey = isset(Yii::app()->session['session_key']) ? Yii::app()->session['session_key'] : null;
            if ($sessionKey == null) {
                    $this->redirect(Yii::app()->homeurl);
            }
            $sessionKey = str_replace(' ', '+', $sessionKey);
            Yii::log("\n SessionKey: " . $sessionKey);
            if (!CUtils::checkAuthSessionKey($sessionKey)) {
                Yii::app()->user->logout();
                Yii::app()->session->clear();
                Yii::app()->session->destroy();
                Yii::app()->user->setFlash('responseToUser', 'Tài khoản Đã bị đăng nhập trên thiêt bị khác');
                $this->redirect(Yii::app()->homeurl . '/account');
                return false;
            }
        }
        return parent::beforeAction($action);
    }
    public function actionView($id){
        $this->titlePage = 'Câu trả lời';
        if(!isset(Yii::app()->session['user_id'])){
            $this->redirect(Yii::app()->homeurl . '/site/');
        }
        $criteria= new CDbCriteria;
       $criteria->addCondition('status <> 15 and status <> 4');
        $criteria->compare('question_id',$id);
        $answer = Answer::model()->findAll($criteria);
        if(count($answer) > 0){
            $this->redirect(Yii::app()->homeurl . '/question/'.$id);
        }
        $criteria= new CDbCriteria;
        $time = time();
        $uid = Yii::app()->session['user_id'];
        $criteria->condition = "end_time > $time";
        $criteria->compare('question_id',$id);
        $criteria->compare('subscriber_id',$uid);
        $arrHoldQuestion = HoldQuestion::model()->findAll($criteria);
        if(count($arrHoldQuestion) == 0){
            $this->redirect(Yii::app()->homeurl . '/question/'.$id);
        }
        $this->render('answer/index', array('question_id'=>$id));
       
    }
    public function actionSaveUpload(){
        header("Content-Type: text/html;charset=utf-8");
        if (isset($_POST) && isset($_FILES)) { 
            $answer = new Answer();
            $answer->question_id = isset($_POST['question_id']) ? $_POST['question_id'] : null;
            $answer->subscriber_id = Yii::app()->session['user_id'];
            $answer->content = isset($_POST['title'])? $_POST['title'] : null;
            $answer->status = 15;//status 15: ko upload dc anh
            $answer->create_date = date('Y-m-d H:i:s');
            $answer->modify_date = date('Y-m-d H:i:s');
            if (!$answer->save()){
                echo "<pre>"; print_r($answer->getErrors());die;
            }else{
                $files = $_FILES['file'];
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['name'][$i] != '') {
                        $filename = $files['name'][$i];

                        $array_name = explode('.', $filename);
                        $extention = $array_name[count($array_name) - 1];
                        $new_name = date('YmdHis') . '-' . rand() . '.' . $extention;

//                        $director = 'upload/' . $answer->id;
                        $director = '/var/www/html/web/uploadanswer/';
                        $director1 = '/var/www/html/web/uploadanswerfull';
                        if (!file_exists($director)) {
                            mkdir($director, 0777, true);
                        }
                        Yii::log('forder upload : '.$director, 'error');
                        move_uploaded_file($files['tmp_name'][$i], $director1 . '/' . $new_name);
                        // Check to make sure the move result is true before continuing
                        $target_file = $director1 . '/' . $new_name;
                        $resized_file = $director . '/' . $new_name;
                        $wmax = 1024;
                        $hmax = 720;
                        $this->ak_img_resize($target_file, $resized_file, $wmax, $hmax, $files['type'][$i]);
                        //
                        $answerImage = new AnswerImage(); 
                        $answerImage->title = isset($_POST['title'])? $_POST['title']:null;
                        $answerImage->title_ascii = isset($_POST['title'])? $_POST['title']:null;
                        $answerImage->answer_id = $answer->id;
                        $answerImage->type = 1;
                        $answerImage->status = 1;
//                        $questionImage->base_url = '/' . $director . '/' . $new_name;
                        $answerImage->base_url = 'web/uploadanswer/' . $new_name;

//                                Yii::log('subscriber_media : '.$media, 'error');
                        if (!$answerImage->save()){
                            $errors = $answerImage->getErrors();
                            $status = '';
                            foreach ($errors as $error){
                                $status = $error[0] . ' ';
                            }
                            echo $status;
                            die;
                        }
                        $size = getimagesize('/var/www/html/web/uploadanswer/'.$new_name);
                        $answerImage->width = $size[0];
                        $answerImage->height = $size[1];
                        $answerImage->save();
                    }
                }
                if(!isset($answerImage->width) || $answerImage->width == 0 || !isset($answerImage->id)){
                    echo 2;die;
                }
                $answer->status = 2;
                $answer->save();
                $questionCheck = Question::model()->findByPk($_POST['question_id']);
                $questionCheck->status = 2;
                $questionCheck->save();
                $Changelevel = ChangeLevel::model()->findByAttributes(array('level_id'=>$questionCheck->level_id, 'question_id'=>$_POST['question_id'], 'status'=>1));
                if($Changelevel != null){
                    $Changelevel->status = 4;
                    $Changelevel->save();
                }
                $this->notifiquestion($questionCheck->subscriber_id, $_POST['question_id']);
                $time = time();
                $holdQuestion = HoldQuestion::model()->findByAttributes(array('question_id'=>$_POST['question_id'], 'subscriber_id'=>$answer['subscriber_id']), "end_time > $time");
                $holdQuestion->status = 1;
                $holdQuestion->end_time = $time;
                $holdQuestion->save();
                $notifiQuestion = NotifiQuestion::model()->findByAttributes(array('class_id'=>$questionCheck['class_id'], 'subject_id'=>$questionCheck['category_id']));
                if($notifiQuestion != null  && $notifiQuestion->count > 0){
                    $notifiQuestion->count -= 1;
                    $notifiQuestion->save();
                }
                // push notification
                $this->notifiquestion($questionCheck->subscriber_id, $_POST['question_id']);
//                $this->notifiquestionEmail($questionCheck->subscriber_id, $_POST['question_id']);
            }

        }
    }
    public function actionStatusSuccess(){
        $answer_id = $_POST['answer_id'];
        $id = $_POST['id'];
        $uid = Yii::app()->session['user_id'];
        $countCheck = AnswerCheck::model()->findAllByAttributes(array('answer_id'=>$answer_id));
        $answerCheck = new AnswerCheck();
        $answerCheck->answer_id = $answer_id;
        $answerCheck->subscriber_id = $uid;
        $answerCheck->status = 1;
        $answerCheck->type = $this->userName->type;
        $answerCheck->count_check += count($countCheck);
        $answerCheck->create_date = date('Y-m-d H:i:s');
        if(!$answerCheck->save()){
            echo '<pre>'; print_r($answerCheck->getErrors());
        }
        $question = Question::model()->findByPk($id);
        $level = Level::model()->findByPk($question['level_id']);
        $answer = Answer::model()->findByPk($answer_id);
       if($question['subscriber_id'] == $uid){
            $answer->status = 6;
            if(!$answer->save()){
                echo '<pre>'; print_r($answer->getErrors());
            }
            $question->status = 6;
            if(!$question->save()){
                echo '<pre>'; print_r($question->getErrors());
            }
            $UserAnswer = Subscriber::model()->findByPk($answer['subscriber_id']);

           $criteria = new CDbCriteria();
           $criteria->compare('subscriber_id', $answer['subscriber_id']);
           $criteria->addCondition('(status = 6 or status = 3)');
           $checkAnser = Answer::model()->count($criteria);
            Yii::log("\n SessionKey: " . $checkAnser);
            if($checkAnser > 5){
                Yii::log("\n SessionKey: " . $UserAnswer->point);
                if($UserAnswer->point <=20 || $UserAnswer->point == null){
                    $UserAnswer->fcoin += ($level->fcoin*20)/100;
                }else
                if($UserAnswer->point > 20 && $UserAnswer->point <=100){
                    $UserAnswer->fcoin += ($level->fcoin*25)/100;
                }else
                if($UserAnswer->point > 100){
                    $UserAnswer->fcoin += ($level->fcoin*30)/100;
                }else{
                    $UserAnswer->fcoin +=$level->fcoin;
                }
            }
            $UserAnswer->point +=$level->point_plus;
            $UserAnswer->save();
        }
    }
    public function actionStatusFail(){
       $answer_id = $_POST['answer_id'];
       $id = $_POST['id'];
       $uid = Yii::app()->session['user_id'];
        $countCheck = AnswerCheck::model()->findAllByAttributes(array('answer_id'=>$answer_id));
        $answerCheck = new AnswerCheck();
        $answerCheck->answer_id = $answer_id;
        $answerCheck->subscriber_id = $uid;
        $answerCheck->status = 2;
        $answerCheck->type = $this->userName->type;
        $answerCheck->count_check += count($countCheck);
        $answerCheck->create_date = date('Y-m-d H:i:s');
        if(!$answerCheck->save()){
            echo '<pre>'; print_r($answerCheck->getErrors());
        }
        $question = Question::model()->findByPk($id);
        $level = Level::model()->findByPk($question['level_id']);
        $answer = Answer::model()->findByPk($answer_id);
        if($question['subscriber_id'] == $uid){
            $answer->status = 5;
            $question->status = 5; // day lai he thong cho admin xac nhan 
           if(!$question->save()){
                echo '<pre>'; print_r($question->getErrors());
            }
            if(!$answer->save()){
                echo '<pre>'; print_r($answer->getErrors());
            }
        }
    }
    function ak_img_resize($target, $newcopy, $w, $h, $ext) {
        $font_size = 20;
        $font_path = "/var/www/html/web/roboto-medium.ttf";
        $water_mark_text = "hocde.vn";
         Yii::log('--------------1--------------');
         list($w_orig, $h_orig) = getimagesize($target);
         Yii::log('--------------2--------------');
         $scale_ratio = $w_orig / $h_orig;
         if (($w / $h) > $scale_ratio) {
                $w = $h * $scale_ratio;
         } else {
                $h = $w / $scale_ratio;
         }      
         $img = "";
         Yii::log('--------------3--------------');
         $ext = strtolower($ext);
         Yii::log('--------------4--------------');
         Yii::log('--------------5--------------'.$ext);
         Yii::log('--------------6--------------'.$target);
         if ($ext == "image/gif"){ 
             $img = imagecreatefromgif($target);
           } else if($ext =="image/png"){ 
             Yii::log('--------------7--------------'.$target);
             $img = imageCreateFromPng($target);
           } else if($ext =="image/bmp"){ 
             Yii::log('--------------7--------------'.$target);
             $img = imagecreatefromwbmp($target);
           } else { 
             Yii::log('--------------7--------------'.$target);
             $img = imageCreateFromJpeg($target);
           } 
         Yii::log('--------------8--------------');
         $tci = imagecreatetruecolor($w, $h);
         Yii::log('--------------9--------------');
         // imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
         imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
         Yii::log('--------------10--------------');
         $blue = imagecolorallocate($tci, 79, 166, 185);
         Yii::log('--------------11--------------'.$target);
         Yii::log('--------------font--------------'.$font_path);
         $x = $w*0.75; $y= $h*0.95;
         imagettftext($tci, $font_size, 0, $x, $y, $blue, $font_path, $water_mark_text);
         imagejpeg($tci, $newcopy, 80);
    }

    public function notifiquestion($subscriber_id, $questionId){
        $sub_question = Subscriber::model()->findByPk($subscriber_id);
        $notification = new Subscriber();
        $content = 'Câu hỏi của bạn đã có câu trả lời';
        if (count($sub_question) > 0) {
            if ($sub_question->device_token != null && $sub_question->device_token != '' && $sub_question->device_token != '(null)') {
                $registatoin_ids = array($sub_question->device_token);
                if ($sub_question->device_type == 1) {
                    $message = $content;
                    foreach ($registatoin_ids as $deviceToken) {
                        $notification->ios_notification($deviceToken, $message);
                    }
                }
                if ($sub_question->device_type == 2) {
                    $message = array(
                        'Title' => 'Học dễ',
                        "Notice" => $content,
                        'Type' => 3,
                        'QuestionId' => $questionId,
                    );
                    $notification->send_notification($registatoin_ids, $message);
                }
            }
        }
    }
    public function notifiquestionEmail($subscriber_id, $questionId){
        $sub_question = Subscriber::model()->findByPk($subscriber_id);
        if($sub_question->email != null || $sub_question->email != ''){
            $content = 'Xin chào '.$sub_question->username.'<br>'.'. Câu hỏi của bạn đã có câu trả lời. Xin cảm ơn!<br>';
            $message = new YiiMailMessage;
            $message->setBody($content, 'text/html');

            $message->subject = "[HocDe] Thông báo câu hỏi có đáp án";
            $message->addTo($sub_question->email);
            $message->from = 'nicespace2015@gmail.com';
            Yii::app()->mail->send($message);
        }
    }
}
?>
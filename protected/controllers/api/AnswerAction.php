<?php

/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 31/7/15
 * Time: 10:18 PM
 * To change this template use File | Settings | File Templates.
 */
class AnswerAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $params = $_POST;
        if (!isset($params['question_id']) || !isset($params['user_id']) || !isset($params['number_image']) || !isset($params['title'])) {
            if (!isset($params['question_id'])) {
                echo json_encode(array('code' => 1, 'message' => 'Missing params question_id'));
                //            return;
            }
            if (!isset($params['user_id'])) {
                echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
                //            return;
            }
            if (!isset($params['number_image'])) {
                echo json_encode(array('code' => 1, 'message' => 'Missing params number_image'));
                //            return;
            }
            if (!isset($params['title'])) {
                echo json_encode(array('code' => 1, 'message' => 'Missing params title'));
                //            return;
            }
            return;
        }
        if (!Question::model()->exists('id = ' . $params['question_id'])) {
            echo json_encode(array('code' => 5, 'message' => 'Question is not exist'));
            return;
        }
        if (!Subscriber::model()->exists('id = ' . $params['user_id'])) {
            echo json_encode(array('code' => 5, 'message' => 'Subscriber is not exist'));
            return;
        }
        if ($params['number_image'] < 1) {
            echo json_encode(array('code' => 1, 'message' => 'Params number_image truyen lon hon 0'));
        }
        $images = array();
        $number_image = $params['number_image'];

        for ($i = 0; $i < $number_image; $i++) {
            if (!isset($params["width_$i"])) {
                echo json_encode(array('code' => 1, 'message' => 'Missing params width_' . $i));
                return;
            }
            if (!isset($params["height_$i"])) {
                echo json_encode(array('code' => 1, 'message' => 'Missing params height_' . $i));
                return;
            }
            if (!isset($params["extension_$i"])) {
                echo json_encode(array('code' => 1, 'message' => 'Missing params extension_' . $i));
                return;
            }
            if (!isset($params["image_" . $i . "_base64"])) {
                echo json_encode(array('code' => 1, 'message' => 'Missing params image_' . $i . '_base64'));
                return;
            }
            $array_image = array(
                'extension' => $params["extension_$i"],
                'image_base64' => $params["image_" . $i . "_base64"],
                'width' => $params["width_$i"],
                'height' => $params["height_$i"],
            );
            array_push($images, $array_image);
        }

        $answer = new Answer();
        $answer->question_id = $params['question_id'];
        $answer->subscriber_id = $params['user_id'];
        $answer->status = 15;
        $answer->content = $params['title']; // tiêu để câu trả lời
        $answer->create_date = date('Y-m-d H:i:s');
        $answer->modify_date = date('Y-m-d H:i:s');

        if (!$answer->save()) {
            echo '<pre>';
            print_r($answer->getErrors());
            die;
            echo json_encode(array('code' => 5, 'message' => 'Cannot upload file'));
            return;
        }

        foreach ($images as $item) {
            $extension = $item['extension'];
            $image_base64 = $item['image_base64'];

            $director = '/var/www/html/web/uploadanswer';
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

            $answer_image = new AnswerImage();
            $answer_image->answer_id = $answer->id;
            $answer_image->type = 1;
            $answer_image->status = 1;
            $answer_image->width = $item['width'];
            $answer_image->height = $item['height'];
            $answer_image->base_url = 'web/uploadanswer/' . $file_name;
//            $question_image->save();
            if (!$answer_image->save()) {
                echo json_encode(array('code' => 5, 'message' => 'Cannot upload images file'));
                return;
            }
            $size = getimagesize('/var/www/html/web/uploadanswer/' . $file_name);
            $answer_image->width = $size[0];
            $answer_image->height = $size[1];
            $answer_image->save();
        }
        if (!isset($answer_image->width) || $answer_image->width == 0 || !isset($answer_image->id)) {
            echo json_encode(array('code' => 5, 'message' => 'Cannot upload images file'));
            return;
        }
        $answer->status = 2;
        $answer->save();
        $question = Question::model()->findByPk($params['question_id']);
        $question->teacher_id = $params['user_id'];
        $question->status = 2;
        $question->save();
        $time = time();
        Yii::log("\n subscriber_id Answer: " . $answer->subscriber_id);
        Yii::log("\n question_id Answer: " . $params['question_id']);
        $holdQuestion = HoldQuestion::model()->findByAttributes(array('question_id'=>$params['question_id'], 'subscriber_id'=>$answer->subscriber_id), "end_time > $time");
        if($holdQuestion != null){
            $holdQuestion->status = 1;
            $holdQuestion->end_time = $time;
            $holdQuestion->save();
        }
        $notifiQuestion = NotifiQuestion::model()->findByAttributes(array('class_id' => $question->class_id, 'subject_id' => $question->category_id));
        if ($notifiQuestion != null && $notifiQuestion->count > 0) {
            $notifiQuestion->count -= 1;
            $notifiQuestion->save();
        }
        header('Content-type: application/json');
        $answer_id = $answer->primaryKey;
        $images = array();
//        $answer_images = AnswerImage::model()->findByAttributes(array('answer_id'=>$answer_id));
//        foreach ($answer_images as $answer_image){
//            $images[] = 'http://27.118.16.139/'.$answer_image['base_url'];
//        }
        $query = "select * from answer_image where answer_id = $answer_id order by id desc";
        $image = AnswerImage::model()->findBySql($query);
        if ($image['width'] != null) {
            $width = $image['width'];
        } else {
            $width = 0;
        }
        if ($image['height'] != null) {
            $height = $image['height'];
        } else {
            $height = 0;
        }
        $sub_name = Subscriber::model()->findByPk($params['user_id']);
        $answer_detail = array(
            'id' => $answer_id,
            'question_id' => $params['question_id'],
            'title' => $params['title'],
            'subscriber_id' => $params['user_id'],
            'username' => $sub_name->firstname . ' ' . $sub_name->lastname,
            'create_date' => date('Y-m-d H:i:s'),
            'status' => 2,
            'url_images' => IPSERVER . $image['base_url'],
            'width' => $width,
            'height' => $height
        );
        $this->notifiquestion($question->subscriber_id, $params['question_id']);
//        $this->notifiquestionEmail($question->subscriber_id, $params['question_id']);
        echo json_encode(array('code' => 0, 'message' => 'Upload successfully', 'item' => $answer_detail));
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
            $message->from = EMAIL;
            Yii::app()->mail->send($message);
        }
    }
}

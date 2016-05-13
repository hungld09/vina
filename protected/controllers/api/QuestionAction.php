<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class QuestionAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        if (!isset($params['title']) || !isset($params['category_id']) || !isset($params['class_id']) || !isset($params['user_id']) || !$params['number_image']){
            if (!isset($params['title'])){
                echo json_encode(array('code' => 5, 'message' => 'Missing params title'));
//            return;
            }
            if(!isset($params['category_id'])){
                echo json_encode(array('code' => 5, 'message' => 'Missing params category_id'));
    //            return;
            }
            if(!isset($params['class_id'])){
                echo json_encode(array('code' => 5, 'message' => 'Missing params class_id'));
    //            return;
            }
            if(!isset($params['user_id'])){
                echo json_encode(array('code' => 5, 'message' => 'Missing params user_id'));
    //            return;
            }
            if(!isset($params['number_image'])){
                echo json_encode(array('code' => 5, 'message' => 'Missing params number_image'));
    //            return;
            }
            return;
        }
        $level_id = isset($params['level_id']) ? $params['level_id']: '1';
        if (!Class1::model()->exists('id = '. $params['class_id'])){
            echo json_encode(array('code' => 5, 'message' => 'Class is not exist'));
            return;
        }
        if (!SubjectCategory::model()->exists('id = '. $params['category_id'])){
            echo json_encode(array('code' => 5, 'message' => 'SubjectCategory is not exist'));
            return;
        }
        if($params['number_image'] < 1){
            echo json_encode(array('code' => 5, 'message' => 'Params number_image truyen lon hon 0'));
            return;
        }
        $images = array();
        $number_image = $params['number_image'];

        for ($i = 0; $i < $number_image; $i++){
            if(!isset($params["width_$i"])){
                echo json_encode(array('code' => 5, 'message' => 'Missing params width_'.$i));
                return;
            }
            if(!isset($params["height_$i"])){
                echo json_encode(array('code' => 5, 'message' => 'Missing params height_'.$i));
                return;
            }
            if(!isset($params["extension_$i"])){
                echo json_encode(array('code' => 5, 'message' => 'Missing params extension_'.$i));
                return;
            }
            if(!isset($params["image_" . $i . "_base64"])){
                echo json_encode(array('code' => 5, 'message' => 'Missing params image_' . $i . '_base64'));
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
        $sub_name = Subscriber::model()->findByPk($params['user_id']);
        $level = Level::model()->findByPk($level_id);
        if((!CUtils::checkTheeQuestion($sub_name->id) && $sub_name->fcoin < $level->fcoin) && !CUtils::promitionFreeQuestion($sub_name->id, $sub_name->partner_id) && !CUtils::promitionFreeGold($sub_name->id)){
           echo json_encode(array('code' => 5, 'message' => 'Tài khoản không đủ tiền')); return;
        }
        $question = new Question();
        $question->title = $params['title'];
        $question->title_ascii = $params['title'];
        $question->category_id = $params['category_id'];
        $question->class_id = $params['class_id'];
        $question->level_id = $level_id;
        $question->subscriber_id = $params['user_id'];
        $question->status = 15;
        $question->create_date = date('Y-m-d H:i:s');
        $question->modify_date = date('Y-m-d H:i:s');

        if (!$question->save()){
            echo json_encode(array('code' => 5, 'message' => 'Cannot upload file'));
            return;
        }
        foreach ($images as $item){
            $extension = $item['extension'];
            $image_base64 = $item['image_base64'];

            $director = '/var/www/html/web/uploadquestion/';
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

            $question_image = new QuestionImage();
            $question_image->question_id = $question->id;
            $question_image->title = $question->title;
            $question_image->title_ascii = $question->title_ascii;
            $question_image->type = 1;
            $question_image->status = 1;
            $question_image->width = $item['width'];
            $question_image->height = $item['height'];
            $question_image->base_url = 'web/uploadquestion/' . $file_name;
//            $question_image->save();
            if (!$question_image->save()){
                echo json_encode(array('code' => 5, 'message' => 'Cannot upload images file'));
                return;
            }
            $size = getimagesize('/var/www/html/web/uploadquestion/'.$file_name);
            $question_image->width = $size[0];
            $question_image->height = $size[1];
            $question_image->save();
        }
        if(!isset($question_image->width) || $question_image->width == 0 || !isset($question_image->id)){
            echo json_encode(array('code' => 5, 'message' => 'Cannot upload images file'));
                return;
        }
        $question->status = 1;
        $question->save();
        $time = date('Y-m-d H:i:s');
        $transaction = $sub_name->newTransactionServiceQuestion(PURCHASE_TYPE_QUESTION, $level->fcoin, $sub_name);
        if(CUtils::promitionFreeGold($sub_name->id)){
            $question = $this->promitionFreeGold($question,$transaction, $sub_name->id);
        }else{
            Yii::log('--------------check Money 1--------------'.$params['user_id']);
            if(CUtils::promitionFreeQuestion($sub_name->id, 'net2e')){
                Yii::log("\n Vao case nay roi: ".$sub_name->partner_id);
                $question = $this->FreeQuestion($question,$transaction, $sub_name->id);
//                return $question;
            }else{
                $question = $this->checkMoney($question,$transaction, $params['user_id'], $sub_name, $level);
            }
        }
        Yii::log("\n IDDDDDDDDDDDDDDDDDDDDDDDDDD: APPPPPPPPPPP" . $question->type . $params['user_id']);
        Yii::log('--------------check Money 7--------------'.$params['user_id']);
        $notifiQuestion = NotifiQuestion::model()->findByAttributes(array('class_id'=>$params['class_id'], 'subject_id'=>$params['category_id']));
        if(count($notifiQuestion) > 0){
            $notifiQuestion->count += 1;
            $notifiQuestion->save();
        }else{
            $notifiQuestion = new NotifiQuestion;
            $notifiQuestion->class_id = $params['class_id'];
            $notifiQuestion->subject_id = $params['category_id'];
            $notifiQuestion->count = 1;
            $notifiQuestion->save();
        }
        header('Content-type: application/json');
        $question_id = $question->primaryKey;
        $images = array();
        $question_images = QuestionImage::model()->findAllByAttributes(array('question_id'=>$question_id));
        foreach ($question_images as $question_image){
            $images[] = IPSERVER.$question_image['base_url'];
        }
//        $sub_name = Subscriber::model()->findByPk($params['user_id']);
        $class_name = Class1::model()->findByPk($params['class_id']);
        $subject_name = SubjectCategory::model()->findByPk($params['category_id']);
        Yii::log('question_id'.$question_id);
        Yii::log('class_name'.$class_name->class_name);
        Yii::log('subject_name'.$subject_name->subject_name);
        $CUtils = new CUtils;
//        $CUtils->notifiquestionEmail($question);
        $question_detail = array(
            'id'=>$question_id,
            'subscriber_id'=>$params['user_id'],
            'username'=>$sub_name->firstname. ' ' .$sub_name->lastname,
            'class_name'=>$class_name->class_name,
            'subject_name'=>$subject_name->subject_name,
            'create_date'=>date('Y-m-d H:i:s'),
            'status'=>1,
            'fcoin'=>$sub_name->fcoin,
            'url_images'=>$images
        );
        echo json_encode(array('code' => 0, 'message' => 'Upload successfully', 'item'=>$question_detail));
    }
    public function promitionFreeGold($question, $transaction, $user_id){
        $goldTime = GoldTime::model()->findByAttributes(array('subscriber_id'=>$user_id, 'type'=>1));
        if($goldTime != null){
            $goldTime->times += 1;
        }else{
            $goldTime = new GoldTime;
            $goldTime->subscriber_id = $user_id;
            $goldTime->times = 1;
            $goldTime->type = 1;
            $goldTime->created_date = time();
        }
        $goldTime->save();
        $question->type = 1; //Câu hỏi free
        $question->level_id = 1; //Câu hỏi free
        $transaction->status = 1;
        $transaction->cost = 0;
        $transaction->save();
        $question->save();
        return $question;
    }
    public function checkMoney($question, $transaction, $user_id, $sub_name, $level = null){
        $time = date('Y-m-d H:i:s');
        Yii::log('--------------total 1--------------'.$time);
        $criteria= new CDbCriteria;
        $criteria->condition = "expiry_date > '$time'";
        $criteria->compare('is_active',1);
        $criteria->compare('subscriber_id',$user_id);
        $usingService = ServiceSubscriberMapping::model()->findAll($criteria);
//        $PromotionSubscriber = PromotionFreeContent::model()->findByAttributes(array('subscriber_id' => $user_id, 'type'=>1));
        Yii::log('--------------check Money 2--------------'.$user_id);
        if (count($usingService) > 0) {
            $subFree = CheckFreeContent::model()->findByAttributes(array('subscriber_id' => $user_id));
            if($subFree != null){
                if($subFree->total < 3){
                    $subFree->total += 1;
                    Yii::log('--------------total 1--------------'.$subFree->total);
                    $subFree->save();
                    Yii::log('--------------total 2--------------'.$subFree->total);
                    $question->type = 1; //Câu hỏi free
                    $question->level_id = 1; //Câu hỏi free
                    $transaction->status = 1;
                    $transaction->cost = 0;
                    $transaction->save();
                    $coin = 0;
                }else{
                    $sub_name->fcoin -= $level->fcoin;
                    $sub_name->save();
                    $question->type = 2; //Câu hỏi mất phí
                    $transaction->status = 1;
                    $transaction->save();
                }
            }else{
                Yii::log('--------------total 3------ chua co khuyen mai--------');
                $checkFreeContent = new CheckFreeContent();
                $checkFreeContent->subscriber_id = $user_id;
                $checkFreeContent->total = 1;
                $checkFreeContent->create_date = time();
                $checkFreeContent->save();
                $question->type = 1; //Câu hỏi free
                $question->level_id = 1; //Câu hỏi free
                $transaction->status = 1;
                $transaction->cost = 0;
                $transaction->save();
            }
            $question->save();
        }else{
            Yii::log('--------------check Money 3--------------'.$user_id);
            Yii::log('--------------check Money 6--------------'.$user_id);
            $sub_name->fcoin -= $level->fcoin;
//                $sub_name->fcoin -= 50;
            $sub_name->save();
            $question->type = 2;
            $transaction->status = 1;
            $transaction->save();
            $question->save();
        }
        return $question;
    }
    public function FreeQuestion($question, $transaction, $user_id){
        Yii::log("\n IDDDDDDDDDDDDDDDDDDDDDDDDDD: " . $user_id);
        $goldTime = PromotionFreeContent::model()->findByAttributes(array('subscriber_id'=>$user_id));
        if($goldTime != null){
            Yii::log("\n case 1: ");
            $goldTime->total += 1;
            $goldTime->save();
        }
        $question->type = 1; //Câu hỏi free
        $question->level_id = 1; //Câu hỏi free
        $transaction->status = 1;
        $transaction->cost = 0;
        $transaction->save();
        $question->save();
        return $question;
    }
}

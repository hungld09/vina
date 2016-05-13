<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/8/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class DetailquestionAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_GET;
        $id = isset($params['question_id']) ? $params['question_id'] : null;
        $subscriberId = isset($params['user_id']) ? $params['user_id'] : null;
        if(!isset($params['type'])){
            echo json_encode(array('code' => 5, 'message' => 'Missing params type'));
            return;
        }
        if (empty($id)){
            echo json_encode(array('code' => 5, 'message' => 'Missing params question_id'));
            return;
        }
        if (empty($subscriberId)){
            echo json_encode(array('code' => 5, 'message' => 'Missing params user_id'));
            return;
        }
        if (!Question::model()->exists('id = '. $id)){
            echo json_encode(array('code' => 5, 'message' => 'question is not exist'));
            return;
        }
        $query = "select * from question where id = $id";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $result = $command->queryAll();
//        print_r($result[0]['id']);die;
        if (count($result) > 0){
            $images_question = QuestionImage::model()->findByAttributes(array('question_id' => $result[0]['id'], 'status' => 1));
            $result[0]['url_images'] = IPSERVER.$images_question['base_url'];
            if($images_question['width'] != null){
                $result[0]['width'] = $images_question['width'];
            }else{
                $result[0]['width'] = 0;
            }
            if($images_question['height'] != null){
                $result[0]['height'] = $images_question['height'];
            }else{
                $result[0]['height'] = 0;
            }
            $class_name = Class1::model()->findByAttributes(array('id' => $result[0]['class_id'], 'status' => 1));
            $subjectCategory = SubjectCategory::model()->findByAttributes(array('id' => $result[0]['category_id'], 'status' => 1));
            $subcriber = Subscriber::model()->findByPk($result[0]['subscriber_id']);
            $result[0]['class_name'] = $class_name['class_name'];
            $result[0]['subject_name'] = $subjectCategory['subject_name'];
            $result[0]['subscriber_name'] = $subcriber['firstname'] . ' ' . $subcriber['lastname'];
            if($subcriber->url_avatar != null){
                if($subcriber['password'] == 'faccebook' || $subcriber['password'] == 'Google'){
                    $url_avatar = $subcriber->url_avatar;
                }else{
                    $url_avatar = IPSERVER . $subcriber->url_avatar;
                }
//                $url_avatar = IPSERVER.$subcriber->url_avatar;
            }else{
                $url_avatar = '';
            }
            $result[0]['url_avatar'] = $url_avatar;
            $subs = Subscriber::model()->findByPk($params['user_id']);
            $result[0]['channel_type'] = $subs['channel_type'];
            if($result[0]['level_id'] == 0 || $result[0]['level_id'] == null || $result[0]['level_id'] == ''){
                $result[0]['level_id'] = 1;
            }
            $Changelevel = ChangeLevel::model()->findByAttributes(array('level_id'=>$result[0]['level_id'], 'question_id'=>$id, 'status'=>1));
            if($Changelevel != null){
                $result[0]['statusLevel'] = 1;
            }else{
                $result[0]['statusLevel'] = 0;
            }
            $checkLike = Like::model()->findByAttributes(
                array(
                    'question_id'=>$result[0]['id'],
                    'subscriber_id'=>$subscriberId
                )
            );
            if(count($checkLike) > 0){
                $result[0]['check_like'] = 1;
            }else{
                $result[0]['check_like'] = 0;
            }
            $query = "select * from comment where question_id = $id";
            $connection = Yii::app()->db;
            $command = $connection->createCommand($query);
            $result[0]['comments'] = $command->queryAll();
            for ($j = 0; $j< count($result[0]['comments']); $j++){
                $checkLike = Like::model()->findByAttributes(
                    array(
                        'comment_id'=>$result[0]['comments'][$j]['id'],
                        'subscriber_id'=>$subscriberId
                    )
                );
                $name = Subscriber::model()->findByPk($result[0]['comments'][$j]['subscriber_id']);
                if($name->url_avatar != null){
                    if($name['password'] == 'faccebook' || $name['password'] == 'Google'){
                        $url_avatar = $name->url_avatar;
                    }else{
                        $url_avatar = IPSERVER . $name->url_avatar;
                    }
//                    $url_avatar = IPSERVER.$name->url_avatar;
                }else{
                    $url_avatar = '';
                }
                $result[0]['comments'][$j]['subscriber_name']= $name->firstname. ' ' .$name->lastname;
//                $result[0]['lastname'] = $name->lastname;
//                $result[0]['firstname'] = $name->firstname;
                $result[0]['comments'][$j]['url_avatar']= $url_avatar;
                if(count($checkLike) > 0){
                    $result[0]['comments'][$j]['check_like'] = 1;
                }else{
                    $result[0]['comments'][$j]['check_like'] = 0;
                }
            }
            //
            $query = "select * from answer where question_id = $id and status <> 4 and status <> 15 order by id desc";
            $connection = Yii::app()->db;
            $command = $connection->createCommand($query);
            $result[0]['answers'] = $command->queryRow();
            $answer_id = $result[0]['answers']['id'];
            $check_user = AnswerCheck::model()->findAllByAttributes(array('answer_id'=>$answer_id, 'subscriber_id'=>$params['user_id'], 'type'=>$params['type']));
            if($result[0]['answers'] !=''){
                $name = Subscriber::model()->findByPk($result[0]['answers']['subscriber_id']);
                if($name->url_avatar != null){
                    if($name['password'] == 'faccebook' || $name['password'] == 'Google'){
                        $url_avatar = $name->url_avatar;
                    }else{
                        $url_avatar = IPSERVER . $name->url_avatar;
                    }
//                    $url_avatar = IPSERVER.$name->url_avatar;
                }else{
                    $url_avatar = '';
                }
                $result[0]['answers']['subscriber_name']= $name['firstname']. ' ' .$name['lastname'];
                if($name['voip'] != null){
                    $result[0]['answers']['mobile']= $name['voip'];
                }else{
                    $result[0]['answers']['mobile']= "";
                }
                $query = "select * from answer_image where answer_id = $answer_id order by id asc";
                $image = AnswerImage::model()->findAllBySql($query);
                //
                $url_images=array();
                for ($j=0; $j<count($image);$j++){
                    $url_images[$j]['images'] = IPSERVER.$image[$j]['base_url'];
        //            $url_images[$j] = IPSERVER.$image[$j]['base_url'];
//                    array_push($url_images, $url_images[$j]);
                    if($image[$j]['width'] != null){
                       $url_images[$j]['width'] =  $image[$j]['width'];
                    }else{
                         $url_images[$j]['width'] = 0;
                    }
                    if($image[$j]['height'] != null){
                       $url_images[$j]['height'] =  $image[$j]['height'];
                    }else{
                         $url_images[$j]['height'] = 0;
                    }
                }
                //
                $result[0]['answers']['url_avatar'] = $url_avatar;
                //
                $result[0]['answers']['url_images'] = $url_images;
//                $result[0]['answers']['url_images'] = IPSERVER.$image['base_url'];
//                $result[0]['answers']['width']= $image['width'];
//                $result[0]['answers']['height']= $image['height'];
                //
                if(count($check_user) > 0){
                    $result[0]['answers']['confirm']= 'yes';
                }else{
                    $result[0]['answers']['confirm']= 'no';
                }
                $checkLike = Like::model()->findByAttributes(
                    array(
                        'answer_id'=>$answer_id,
                        'subscriber_id'=>$subscriberId
                    )
                );
                if(count($checkLike) > 0){
                    $result[0]['answers']['check_like'] = 1;
                }else{
                    $result[0]['answers']['check_like'] = 0;
                }
            }else{
                $result[0]['answers'] = '';
            }
//            print_r($result[0]['answers']);die;
        }   
        echo json_encode(array('code' => 0, 'item'=>$result[0]));
    }
}

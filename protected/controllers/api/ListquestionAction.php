<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ListquestionAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $page = isset($_GET['page_number']) ? $_GET['page_number'] : 1;
        $limit = isset($_GET['page_size']) ? $_GET['page_size'] : 20;
        $userId = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
        $type = isset($_GET['type']) ? $_GET['type'] : 0;
        $status = isset($_GET['status']) ? $_GET['status'] : 0;
        $class = isset($_GET['class_id']) ? $_GET['class_id'] : 0;
        $subject = isset($_GET['category_id']) ? $_GET['category_id'] : 0;

        if($userId == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params user_id'));
            return;
        }
        if($type == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params type'));
            return;
        }
        if($status == null){
            echo json_encode(array('code' => 1, 'message' => 'Missing params status'));
            return;
        }

        $offset = ($page - 1) * $limit;
        $question = Question::model()->getQuestion($limit,$offset,$type,$status,$userId,$class,$subject);
        $question =$question['data'];
        for ($i = 0; $i < count($question); $i++){
            $question_id = $question[$i]['id'];
            $class_id = $question[$i]['class_id'];
            $category_id = $question[$i]['category_id'];
            $subcriber_id = $question[$i]['subscriber_id'];
            $question_image = QuestionImage::model()->findByAttributes(array('question_id' => $question_id, 'status' => 1));
            $class_name = Class1::model()->findByAttributes(array('id' => $class_id, 'status' => 1));
            $subjectCategory = SubjectCategory::model()->findByAttributes(array('id' => $category_id, 'status' => 1));
            $Subcriber = Subscriber::model()->findByPk($subcriber_id);

            $checkLike = Like::model()->findByAttributes(
                array(
                    'question_id'=>$question_id,
                    'subscriber_id'=>$userId
                )
            );

            if($Subcriber['url_avatar'] != null){
                if($Subcriber['password'] == 'faccebook' || $Subcriber['password'] == 'Google'){
                    $url_avatar = $Subcriber['url_avatar'];
                }else{
                    $url_avatar = IPSERVER . $Subcriber['url_avatar'];
                }
//                $url_avatar = IPSERVER.$Subcriber['url_avatar'];
            }else{
                $url_avatar = '';
            }
            $question[$i]['class_name'] = $class_name['class_name'];
            $question[$i]['subject_name'] = $subjectCategory['subject_name'];
            $question[$i]['subscriber_name'] = $Subcriber['firstname'] . ' ' . $Subcriber['lastname'];
            $question[$i]['lastname'] = $Subcriber['lastname'];
            $question[$i]['firstname'] = $Subcriber['firstname'];
            $question[$i]['url_avatar'] = $url_avatar;
            $question[$i]['url_images'] = IPSERVER.$question_image['base_url'];
            if($question_image['width'] != null){
                $question[$i]['width'] = $question_image['width'];
            }else{
                $question[$i]['width'] = 0;
            }
            if($question_image['height'] != null){
                $question[$i]['height'] = $question_image['height'];
            }else{
                $question[$i]['height'] = 0;
            }
            if(count($checkLike) > 0){
                $question[$i]['check_like'] = 1;
            }else{
                $question[$i]['check_like'] = 0;
            }
        }
        echo json_encode(array('code' => 0,'Question' => array('title' => 'Danh sách câu hỏi','items'=>$question)));
    }   
}
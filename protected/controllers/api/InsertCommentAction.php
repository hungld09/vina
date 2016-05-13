<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class InsertCommentAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        $subscriberId = isset($params['subscriber_id']) ? $params['subscriber_id'] : null;
        $questionId = isset($params['question_id']) ? $params['question_id'] : null;
        $content = isset($params['content']) ? $params['content'] : null;


        if($subscriberId == null || $questionId == null || $content == null){
            if($subscriberId == null){
                echo json_encode(array('code' => 5, 'message' => 'Missing params subscriber_id'));
            }
            if($questionId == null){
                echo json_encode(array('code' => 5, 'message' => 'Missing params question_id'));
            }
            if($content == null){
                echo json_encode(array('code' => 5, 'message' => 'Missing params content'));
            }
            return;
        }

        $comment = new Comment();
        $comment->subscriber_id = $subscriberId;
        $comment->question_id = $questionId;
        $comment->content = $content;
        $comment->create_date = date('Y-m-d H:i:s');
        if($comment->save()){
            $question = Question::model()->findByPk($questionId);
            $question->count_comment += 1;
            if($question->save()){
                $Subcriber = Subscriber::model()->findByPk($subscriberId);
                if($Subcriber['url_avatar'] != null){
                    $url_avatar = IPSERVER.$Subcriber['url_avatar'];
                }else{
                    $url_avatar = '';
                }
                echo json_encode(
                    array(
                        'code' => 0,
                        'comment' => array(
                            'id' => $comment->id,
                            'create_date' => $comment->create_date,
                            'content' => $comment->content,
                            'count_comment_question' => $question->count_comment,
                            'subscriber_name' => $Subcriber['firstname'] . ' ' . $Subcriber['lastname'],
                            'lastname' => $Subcriber['lastname'],
                            'firstname' => $Subcriber['firstname'],
                            'url_avatar' => $url_avatar,
                            'message' => 'Comment successfully',
                        ),
                    )
                );
                return;
            }
        }else{
            echo json_encode(array('code' => 5, 'message' => 'Comment failed'));
            return;
        }
    }
}
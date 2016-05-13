<?php

/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */
class LikequestionAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $params = $_POST;
        $subscriberId = isset($params['user_id']) ? $params['user_id'] : null;

        $questionId = isset($params['question_id']) ? $params['question_id'] : null;
        $commentId = isset($params['comment_id']) ? $params['comment_id'] : null;
        $answerId = isset($params['answer_id']) ? $params['answer_id'] : null;

        if ($subscriberId == null) {
            if ($subscriberId == null) {
                echo json_encode(array('code' => 5, 'message' => 'Missing params user_id'));
            }
            return;
        }
        if ($questionId != null && $commentId != null && $answerId != null) {
            echo json_encode(array('code' => 5, 'message' => 'Params question_id vs comment_id vs answer_id không được gửi cùng lúc'));
            return;
        }
        if ($questionId != null) {
            $checkLike = Like::model()->findByAttributes(
                array(
                    'subscriber_id' => $subscriberId,
                    'question_id' => $questionId,
                )
            );
            if (count($checkLike) > 0) {
                if ($checkLike->delete()) {
                    $question = Question::model()->findByPk($questionId);
                    $question->count_like -= 1;
                    if ($question->save()) {
                        echo json_encode(array(
                            'code' => 0,
                            'message' => 'Dislike question successfully',
                            'like' => array(
                                'count_like' => $question->count_like,
                                'type_like' => 'question',
                                'question_id' => $questionId,
                                'status' => 'dislike'
                            )
                        ));
                        return;
                    }
                }
                echo json_encode(array('code' => 5, 'message' => 'Dislike question failed'));
                return;
            }
        }
        if ($commentId != null) {
            $checkLike = Like::model()->findByAttributes(
                array(
                    'subscriber_id' => $subscriberId,
                    'comment_id' => $commentId,
                )
            );
            if (count($checkLike) > 0) {
                if ($checkLike->delete()) {
                    $cm = Comment::model()->findByPk($commentId);
                    $cm->count_like -= 1;
                    if ($cm->save()) {
                        echo json_encode(array(
                            'code' => 0,
                            'message' => 'Dislike comment successfully',
                            'like' => array(
                                'count_like' => $cm->count_like,
                                'type_like' => 'comment',
                                'comment_id' => $commentId,
                                'status' => 'dislike'
                            )
                        ));
                        return;
                    }
                }
                echo json_encode(array('code' => 5, 'message' => 'Dislike comment failed'));
                return;
            }
        }
        if ($answerId != null) {
            $checkLike = Like::model()->findByAttributes(
                array(
                    'subscriber_id' => $subscriberId,
                    'answer_id' => $answerId,
                )
            );
            if (count($checkLike) > 0) {
                if ($checkLike->delete()) {
                    $answer = Answer::model()->findByPk($answerId);
                    $answer->count_like -= 1;
                    if ($answerId->save()) {
                        echo json_encode(array(
                            'code' => 0,
                            'message' => 'Dislike Answer successfully',
                            'like' => array(
                                'count_like' => $answerId->count_like,
                                'type_like' => 'answer',
                                'answer_id' => $answerId,
                                'status' => 'dislike'
                            )
                        ));
                        return;
                    }
                }
                echo json_encode(array('code' => 5, 'message' => 'Dislike Answer failed'));
                return;
            }
        }
        $like = new Like();
        $like->subscriber_id = $subscriberId;
        if ($questionId != null) {
            $like->question_id = $questionId;
        }
        if ($commentId != null) {
            $like->comment_id = $commentId;
        }
        if ($answerId != null) {
            $like->answer_id = $answerId;
        }
        if ($like->save()) {
            if ($questionId != null) {
                $question = Question::model()->findByPk($questionId);
                $question->count_like += 1;
                if ($question->save()) {
                    echo json_encode(array(
                        'code' => 0,
                        'message' => 'Like question successfully',
                        'like' => array(
                            'count_like' => $question->count_like,
                            'type_like' => 'question',
                            'question_id' => $questionId,
                            'status' => 'like'
                        )
                    ));
                    return;
                }
            }
            if ($commentId != null) {
                $cm = Comment::model()->findByPk($commentId);
                $cm->count_like += 1;
                if ($cm->save()) {
                    echo json_encode(array(
                        'code' => 0,
                        'message' => 'Like comment successfully',
                        'like' => array(
                            'count_like' => $cm->count_like,
                            'type_like' => 'comment',
                            'comment_id' => $commentId,
                            'status' => 'like'
                        )
                    ));
                    return;
                }
            }
            if ($answerId != null) {
                $answer = Answer::model()->findByPk($answerId);
                $answer->count_like += 1;
                if ($answer->save()) {
                    echo json_encode(array(
                        'code' => 0,
                        'message' => 'Like Answer successfully',
                        'like' => array(
                            'count_like' => $answer->count_like,
                            'type_like' => 'answer',
                            'answer_id' => $answerId,
                            'status' => 'like'
                        )
                    ));
                    return;
                }
            }
        } else {
            echo json_encode(array('code' => 5, 'message' => 'Like failed'));
            return;
        }
    }
}
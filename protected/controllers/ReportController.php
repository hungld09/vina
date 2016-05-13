<?php

/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 23/11/15
 * Time: 10:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ReportController extends Controller
{
    public function beforeAction($action){
        if (strcasecmp($action->id, 'index') == 0) {
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
    public function actionIndex(){
//        $this->titlePage = 'Câu hỏi';
//        if(!Yii::app()->session['user_id']){
//            $this->redirect(Yii::app()->homeurl);
//        }
//        $this->render('question/index', array());
    }
    public function actionReport(){
        $type = $_POST['type'];
        $user_id = $_POST['user_id'];
        $question_id = $_POST['question_id'];
        if($user_id == -1){
            echo 5;die;
        }
//        $question = Question::model()->findByPk($question_id);
        $report = new ReportQuestion;
        $report->subscriber_id= $user_id;
        $report->question_id= $question_id;
        $report->created_date= date('Y-m-d H:i:s');
//        if($type == 1){
//            $report->type= $type;
//            $question->status = 9;
//        } else 
        if($type == 2){
            $report->type= $type;
//            $question->status = 10;
        } else if($type == 3){
            $report->type= $type;
//            $question->status = 11;
        } else if($type == 4){
            $report->type= $type;
//            $question->status = 12;
        }else{
            $report->type= $type;
//            $question->status = 9;
        }
        if(!$report->save()){
            var_dump($report->getErrors());die;
        }
//        if(!$question->save()){
//           var_dump($question->getErrors());die;
//        }
//        $notifiQuestion = NotifiQuestion::model()->findByAttributes(array('class_id'=>$question->class_id, 'subject_id'=>$question->category_id));
//        if(count($notifiQuestion) > 0  && $notifiQuestion->count > 0){
//            $notifiQuestion->count -= 1;
//            $notifiQuestion->save();
//        }
//        $result = ReportQuestion::model()->findAllByAttributes(array('subscriber_id'=>$question->subscriber_id));
//        if(count($result) >= 3){
//            $subscriber = Subscriber::model()->findByPk($question->subscriber_id);
//            $subscriber->status = 5;//status = 3 bi report
//            $subscriber->save();
//        }
        echo 11;die;
    }
}
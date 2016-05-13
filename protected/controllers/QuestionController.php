<?php

/*
 * Hungld
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class QuestionController extends Controller {

    public function beforeAction($action) {
//        if (strcasecmp($action->id, 'upload') == 0 || strcasecmp($action->id, 'questionSubject') == 0 || strcasecmp($action->id, 'view') == 0 && strcasecmp($action->id, 'index') == 0 && strcasecmp($action->id, 'listClass') == 0) {
//            $sessionKey = isset(Yii::app()->session['session_key']) ? Yii::app()->session['session_key'] : null;
//            if ($sessionKey == null) {
//                    $this->redirect(Yii::app()->homeurl);
//            }
//            $sessionKey = str_replace(' ', '+', $sessionKey);
//            Yii::log("\n SessionKey: " . $sessionKey);
//            if (!CUtils::checkAuthSessionKey($sessionKey)) {
//                Yii::app()->user->logout();
//                Yii::app()->session->clear();
//                Yii::app()->session->destroy();
//                Yii::app()->user->setFlash('responseToUser', 'Tài khoản Đã bị đăng nhập trên thiêt bị khác');
//                $this->redirect(Yii::app()->homeurl . '/account');
//                return false;
//            }
//        }
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $this->titlePage = 'Câu hỏi';
        if (!Yii::app()->session['user_id']) {
            $this->redirect(Yii::app()->homeurl);
        }
        $this->render('question/index', array());
    }

    public function actionListClass() {
        $this->titlePage = 'Danh sách lớp';
        if (!isset(Yii::app()->session['user_id'])) {
            $this->redirect(Yii::app()->homeurl);
        }
        $user_id = Yii::app()->session['user_id'];
//        $class_id = array();
//        $subscriber = SubscriberCheckTest::model()->findAllByAttributes(array('subscriber_id'=>$user_id), 'point >=18');
//        for ($i=0; $i < count($subscriber); $i++){
//            $id= $subscriber[$i]['class_id'];
//            array_push($class_id, $id);
//        }
//        $array_id = implode(",",$class_id);
//        if($array_id == '' || $array_id == null){
//            $this->redirect(Yii::app()->homeurl);
//        }
//        $query = "select * from class where id in ($array_id)";
        $query = "select * from class";
        $connection = Yii::app()->db2;
        $command = $connection->createCommand($query);
        $class = $command->queryAll();
        $this->render('question/listClass', array(
            'class' => $class,
            'user_id' => $user_id,
        ));
    }

    public function actionNotifyQuestion() {
        $this->titlePage = 'Câu hỏi';
        if (isset($_GET['classId'])) {
            $class_id = $_GET['classId'];
        } else {
            echo 'Url không tồn tại!';
            die;
        }

        $this->render('question/notifyQuestion', array(
            'class_id' => $class_id,
        ));
    }

    public function actionList() {
        $this->titlePage = 'Danh sách câu hỏi';
        $title = isset($_GET['title']) ? $_GET['title'] : '';
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $page_size = 10;
        if (isset($_GET['page'])) {
            $offset = $page_size * $page;
        } else {
            $offset = 0;
        }
        $query = "select * from question where status = 3 order by create_date desc limit $offset, $page_size";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $question = $command->queryAll();
        for ($i = 0; $i < count($question); $i++) {
            $question_id = $question[$i]['id'];
            $class_id = $question[$i]['class_id'];
            $category_id = $question[$i]['category_id'];
            $subcriber_id = $question[$i]['subscriber_id'];
            $questionImage = QuestionImage::model()->findByAttributes(array('question_id' => $question_id, 'status' => 1));
            $question[$i]['base_url'] = $questionImage['base_url'];
            $class_name = Class1::model()->findByAttributes(array('id' => $class_id, 'status' => 1));
            $subjectCategory = SubjectCategory::model()->findByAttributes(array('id' => $category_id, 'status' => 1));
            $question[$i]['class_name'] = $class_name['class_name'];
            $question[$i]['subject_name'] = $subjectCategory['subject_name'];
            $Subcriber = Subscriber::model()->findByPk($subcriber_id);
            $question[$i]['subscriber_name'] = $Subcriber['subscriber_number'];
            $question[$i]['sub_id'] = $Subcriber['id'];
        }
        $this->render('question/list', array(
            'questions' => $question
        ));
    }

    public function actionShowsubject() {
        $class_id = $_POST['classId'];
        if (!isset(Yii::app()->session['user_id'])) {
            $this->redirect(Yii::app()->homeurl);
        }
        $user_id = Yii::app()->session['user_id'];
        $subject_id = array();
        $query = "select * from subject_category where class_id= $class_id and type = 1";
        $connection = Yii::app()->db2;
        $command = $connection->createCommand($query);
        $subject = $command->queryAll();
        $this->renderPartial('question/_showcontent', array(
            'subject' => $subject,
            'class_id' => $class_id,
        ));
    }

    public function actionUpload() {
        $this->titlePage = 'Câu hỏi';
        $class = Class1::model()->findAllByAttributes(array('status' => 1));
        $subject = SubjectCategory::model()->findAllByAttributes(array('status' => 1, 'type' => 1));
        $time = date('Y-m-d H:i:s');
        $criteria = new CDbCriteria;
        $criteria->condition = "is_active = 1 and expiry_date > '$time'";
        $criteria->compare('subscriber_id', Yii::app()->session['user_id']);
        $usingService = ServiceSubscriberMapping::model()->findAll($criteria);
        $level = Level::model()->findAll();
        $this->render('question/upload', array(
            'class' => $class,
            'subject' => $subject,
            'level' => $level
        ));
    }

    public function actionSaveUpload() {
        header("Content-Type: text/html;charset=utf-8");
        if (isset($_POST) && isset($_FILES)) {
            $question = new Question();
            $question->title = isset($_POST['title']) ? $_POST['title'] : null;
            $question->title_ascii = isset($_POST['title']) ? $_POST['title'] : null;
            $question->category_id = isset($_POST['subject']) ? $_POST['subject'] : null;
            $question->class_id = isset($_POST['class1']) ? $_POST['class1'] : null;
            $question->subscriber_id = $this->id;
            $question->status = 15;
            $question->create_date = date('Y-m-d H:i:s');
            $question->modify_date = date('Y-m-d H:i:s');
            // tru tien
            $fcoin = 5000;
            $subscriber = Subscriber::model()->findByPk($this->id);
//            if($subscriber->fcoin < $fcoin){
//                echo 1;die;
//            }
            $transaction = $subscriber->newTransactionServiceQuestion(PURCHASE_TYPE_QUESTION, $fcoin, $subscriber);
            if (!$question->save()) {
                echo "<pre>";
                print_r($question->getErrors());
                die;
            } else {
                $files = $_FILES['file'];
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['name'][$i] != '') {
                        $filename = $files['name'][$i];
//                        $size = getimagesize($filename);
//                        print_r($size);die;
                        $array_name = explode('.', $filename);
                        $extention = $array_name[count($array_name) - 1];
                        $new_name = date('YmdHis') . '-' . rand() . '.' . $extention;

//                        $director = 'upload/' . $question->id;
                        $director = '/var/www/html/web/vina';
                        $director1 = '/var/www/html/web/vina';
                        if (!file_exists($director)) {
                            mkdir($director, 0777, true);
                        }
                        Yii::log('forder upload : ' . $director, 'error');
                        move_uploaded_file($files['tmp_name'][$i], $director1 . '/' . $new_name);
                        // test upload imags
//                        move_uploaded_file($files['tmp_name'][$i], "/var/www/html/web/test/$filename");
                        // Check to make sure the move result is true before continuing
                        $target_file = $director1 . '/' . $new_name;
                        $resized_file = $director . '/' . $new_name;
                        $wmax = 1024;
                        $hmax = 720;
                        $this->ak_img_resize($target_file, $resized_file, $wmax, $hmax, $files['type'][$i]);
                        //
                        $questionImage = new QuestionImage();
                        $questionImage->title = isset($_POST['title']) ? $_POST['title'] : null;
                        $questionImage->title_ascii = isset($_POST['title']) ? $_POST['title'] : null;
                        $questionImage->question_id = $question->id;
                        $questionImage->type = 1;
                        $questionImage->status = 1;
//                        $questionImage->base_url = '/' . $director . '/' . $new_name;
                        $questionImage->base_url = 'web/vina' . $new_name;

//                                Yii::log('subscriber_media : '.$media, 'error');
                        if (!$questionImage->save()) {
                            $errors = $questionImage->getErrors();
                            $status = '';
                            foreach ($errors as $error) {
                                $status = $error[0] . ' ';
                            }
                            echo $status;
                            die;
                        }
                        $size = getimagesize('/var/www/html/web/vina/' . $new_name);
                        $questionImage->width = $size[0];
                        $questionImage->height = $size[1];
                        $questionImage->save();
                    }
                }
                if (!isset($questionImage->width) || $questionImage->width == 0 || !isset($questionImage->id)) {
                    echo 2;
                    die;
                }
                $question->status = 1;
                $question->save();
                $checkThee = $this->checkTheeQuestion($subscriber, $question, $transaction, $fcoin);
                $CUtils = new CUtils();
                $CUtils->notifiquestionEmail($question);
            }
        }
    }

    public function actionView($id) {
        $this->titlePage = 'Câu hỏi';
        if (!isset($id)) {
            $this->redirect(Yii::app()->homeurl);
        }
        $questionCheck = Question::model()->findByPk($id);
        if ($questionCheck == null || $questionCheck->status == 9 || $questionCheck->status == 10 || $questionCheck->status == 11 || $questionCheck->status == 12) {
            $this->redirect(Yii::app()->homeurl);
        }
        $query = "select *, qm.question_id, q.id, q.status as status_q from question q join question_image qm on qm.question_id = q.id where q.id = $id";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $question = $command->queryRow();
        $class_id = $question['class_id'];
        $category_id = $question['category_id'];
        $subcriber_id = $question['subscriber_id'];
        $class_name = Class1::model()->findByAttributes(array('id' => $class_id, 'status' => 1));
        $level = Level::model()->findByPk($question['level_id']);
        $subjectCategory = SubjectCategory::model()->findByAttributes(array('id' => $category_id, 'status' => 1));
        $question['class_name'] = $class_name['class_name'];
        $question['subject_name'] = $subjectCategory['subject_name'];
        $Subcriber = Subscriber::model()->findByPk($subcriber_id);
        $question['subscriber_name'] = $Subcriber['subscriber_number'];
        $question['sub_id'] = $Subcriber['id'];
        //answer
//        $query = "select *, ai.answer_id, a.id, a.question_id, ai.status, ai.title from answer a join answer_image ai on ai.answer_id = a.id where a.question_id = $id and ai.status <> 4 order by a.id desc";
        $query = "select * from answer where question_id = $id and status <> 15 and status <> 4 order by id desc";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $answer = $command->queryRow();
//        echo '<pre>';print_r($answer);die;
        $success = '';
        $checkLike = '';
        $subUser = '';
        $reply = '';
        $arrHoldQuestion = '';
        $url_images = array();
        $subUser = Subscriber::model()->findByPk($answer['subscriber_id']);
        $answer_id = $answer['id'];
        if ($this->msisdn != '') {
            if ($answer != '') {
                $query = "select * from answer_image where answer_id = $answer_id order by id desc";
                $image = AnswerImage::model()->findAllBySql($query);
                //
                for ($j = 0; $j < count($image); $j++) {
                    $url_images[$j]['images'] = IPSERVER . $image[$j]['base_url'];
                    if ($image[$j]['width'] != null) {
                        $url_images[$j]['width'] = $image[$j]['width'];
                    } else {
                        $url_images[$j]['width'] = 0;
                    }
                    if ($image[$j]['height'] != null) {
                        $url_images[$j]['height'] = $image[$j]['height'];
                    } else {
                        $url_images[$j]['height'] = 0;
                    }
                }
                $answer['url_images'] = $url_images;
            }
            $time = time();
            $criteria = new CDbCriteria;
            $criteria->condition = "end_time > $time";
            $criteria->compare('question_id', $id);
            $arrHoldQuestion = HoldQuestion::model()->findAll($criteria);
        } else {
            $answer['url_images'] = $url_images;
        }
        $this->render('question/detail', array('question' => $question, 'answer' => $answer, 'subUser' => $subUser, 'id' => $id, 'reply' => $reply));
    }

    public function actionInsertComment() {
        $user_id = $_POST['uid'];
        $comment_text = $_POST['comment_text'];
        $question_id = $_POST['question_id'];
        $comment = new Comment();
        $comment->subscriber_id = $user_id;
        $comment->content = $comment_text;
        $comment->question_id = $question_id;
        $comment->create_date = date('Y-m-d H:i:s');
        if (!$comment->save()) {
            echo '<pre>';
            print_r($comment->getErrors());
        }
        return;
    }

    public function actionLoadComment() {
        $question_id = $_POST['question_id'];
        $query = "select cm.*,cm.subscriber_id, sub.id, cm.id, sub.url_avatar, sub.lastname, sub.password, sub.firstname from comment cm join subscriber sub on cm.subscriber_id = sub.id where cm.question_id=$question_id order by cm.id desc";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $comments = $command->queryAll();
        $url = Yii::app()->theme->baseUrl;
        $html = '';
        $html .='<ul>';
        $CUtils = new CUtils();
        foreach ($comments as $comment) {
            $time = $CUtils->formatTime($comment['create_date']);
            if ($comment['url_avatar'] == '') {
                $avata = Yii::app()->theme->baseUrl . '/FileManager/avata.png';
            } else {
                if ($comment['password'] == 'faccebook' || $comment['password'] == 'Google') {
                    $avata = $comment['url_avatar'];
                } else {
                    $avata = IPSERVER . $comment['url_avatar'];
                }
            }
            $html .='<li>';
            $html .='<img src="' . $avata . '"/>';
            $html .='<div class="comment-answer-list-title">';
            $html .='<a href="#">' . $comment['lastname'] . ' ' . $comment['firstname'] . '</a>';
            $html .='<p>' . $comment['content'] . '</p>';
            $html .='<p style="color: #b0b0b0;font-family: font-dep1;">' . $time . '</p>';
            $html .='</a">';
            $html .='</div">';
            $html .='</li>';
        }
        $html .='</ul>';
        echo $html;
    }

    public function actionLoadItem() {
        $uid = $_POST['uid'];
        $tab_item = $_POST['tab_item'];
        $page = $_POST['page'];
        $page_size = $_POST['page_size'];
        $offset = $page_size * $page;
        switch ($tab_item) {
            case 1: $status = 3;
                break;
            case 2: $status = 2;
                break;
            case 3: $status = 1;
                break;
            case 4: $status = 5;
                break;
            case 5: $status = 6;
                break;
            default: $status = 2;
                break;
        }
        if ($tab_item == 1) {
            unset(Yii::app()->session['tab_item']);
            Yii::app()->session['tab_item'] = 1;
        } else if ($tab_item == 2) {
            unset(Yii::app()->session['tab_item']);
            Yii::app()->session['tab_item'] = 2;
        } else if ($tab_item == 3) {
            unset(Yii::app()->session['tab_item']);
            Yii::app()->session['tab_item'] = 3;
        } else if ($tab_item == 4) {
            unset(Yii::app()->session['tab_item']);
            Yii::app()->session['tab_item'] = 4;
        } else if ($tab_item == 5) {
            unset(Yii::app()->session['tab_item']);
            Yii::app()->session['tab_item'] = 5;
        }
        unset(Yii::app()->session['page']);
        Yii::app()->session['page'] = $page;
        $html = '';
//        $query ="select * from question where subscriber_id= $uid and status = $status order by id desc";
        if ($status != 5 && $status != 6) {
            if ($this->userName->type == 1) {
                if ($status == 3) {
                    $query = "select *, qm.question_id, q.subscriber_id, q.status,  q.modify_date as modify_date, q.id from question q join question_image qm on qm.question_id = q.id where q.subscriber_id= $uid and (q.status = $status or q.status = 6) order by q.modify_date desc limit $offset, $page_size";
                } else {
                    $query = "select *, qm.question_id, q.subscriber_id, q.status,  q.modify_date as modify_date, q.id from question q join question_image qm on qm.question_id = q.id where q.subscriber_id= $uid and q.status = $status order by q.modify_date desc limit $offset, $page_size";
                }
            } else {
                $query = "select *, qm.question_id, q.subscriber_id, q.status,  q.modify_date as modify_date, q.id from question q join question_image qm on qm.question_id = q.id where q.status = $status order by q.modify_date desc limit $offset, $page_size";
            }
        } else if ($status == 5) {
            $query = "select *, qm.question_id, q.subscriber_id, q.status, q.modify_date as modify_date, q.id from question q join question_image qm on qm.question_id = q.id join (select question_id from answer where subscriber_id = $uid and (status = 3 or status = 6)) as a on a.question_id = q.id order by q.modify_date desc limit $offset, $page_size";
        } else {
            $query = "select *, qm.question_id, q.subscriber_id, q.status, q.modify_date as modify_date, q.id from question q join question_image qm on qm.question_id = q.id join (select question_id from answer where subscriber_id = $uid and status = 4) as a on a.question_id = q.id order by q.modify_date desc limit $offset, $page_size";
        }
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $question = $command->queryAll();
        if (count($question) == 0) {
            $html .='<div class="web_body">';
            $html .='<div class="listarticle">';
            $html .='<div class="row ">';
            $html .='<div class="col-md-12"><div class="row">';
            $html .='<div class="col-md-6 col-xs-6 avata">';
            $html .='Không có kết quả';
            $html .='</div>';
            $html .='</div></div>';
            $html .='</div>';
            $html .='</div>';
            $html .='</div>';
            echo $html;
            die;
        }
        $this->renderPartial('question/autoload', array(
            'question' => $question,
            'user_id' => $uid,
            'tab_item' => $tab_item,
            'status' => $status
        ));
    }

    public function actionLoadSubject() {
        $class_id = $_POST['class_id'];
        $query = "select * from subject_category where class_id = $class_id and type = 1";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $subject = $command->queryAll();
        $html = '';
        if (count($subject) > 0) {
            for ($i = 0; $i < count($subject); $i++) {
                $html .='<option value="' . $subject[$i]['id'] . '">' . $subject[$i]['subject_name'] . '</option>';
            }
        } else {
            $html .='<option value="-1">Chưa cập nhật</option>';
        }
        echo $html;
    }

    public function actionCheckSocket() {
        $test = $_POST['json'];
        if (isset($test['lstid'][3])) {
            echo $test['lstid'][3];
            die;
        } else {
            echo 2;
            die;
        }
    }

    public function actionHoldQuestion() {
        $questionId = $_POST['questionId'];
        $user_id = $_POST['user_id'];
        $status = '';
        if ($user_id == -1) {
            $status = 0;
            echo $status;
            die;
        }
        $time = time();
        $criteria = new CDbCriteria;
        $criteria->condition = "end_time > $time";
        $criteria->compare('question_id', $questionId);
        $arrHoldQuestion = HoldQuestion::model()->findAll($criteria);
        if (count($arrHoldQuestion) > 0) {
            $check = new CDbCriteria;
            $check->condition = "subscriber_id = $user_id and end_time > $time";
            $check->compare('question_id', $questionId);
            $checkUser = HoldQuestion::model()->findAll($check);
            if (count($checkUser) > 0) {
                $status = 3;
                echo $status;
                die;
            } else {
                $status = 1;
                echo $status;
                die;
            }
        }
        $checkGhim = new CDbCriteria;
        $checkGhim->condition = "end_time > $time";
        $checkGhim->compare('subscriber_id', $user_id);
        $checkOnemore = HoldQuestion::model()->findAll($checkGhim);
        if (count($checkOnemore) > 0) {
            $status = 4;
            echo $status;
            die;
        }
        $question = Question::model()->findByPk($questionId);
        $level = Level::model()->findByPk($question->level_id);
        $moreTime = $level->time * 60;
        $holdQuestion = new HoldQuestion();
        $holdQuestion->question_id = $questionId;
        $holdQuestion->subscriber_id = $user_id;
        $holdQuestion->start_time = time();
        $holdQuestion->end_time = time() + $moreTime;
        if (!$holdQuestion->save()) {
            echo '<pre>';
            var_dump($holdQuestion->getErrors());
        }
        $status = $moreTime;
        echo $status;
        die;
    }

    public function actionDeleteholdQuestion($id) {
        $questionId = $id;
        $user_id = Yii::app()->session['user_id'];
        $result = HoldQuestion::model()->deleteAllByAttributes(array('question_id' => $questionId, 'subscriber_id' => $user_id));
        $this->redirect(Yii::app()->baseUrl . '/question/' . $id);
    }

    public function actionQuestionSubject($id) {
        $this->titlePage = 'Câu hỏi';
//            if(!Yii::app()->session['user_id']){
//                $this->redirect(Yii::app()->homeurl.'/account');
//            }
        if (!isset(Yii::app()->session['user_id'])) {
            $this->redirect(Yii::app()->homeurl);
        }
        $user_id = Yii::app()->session['user_id'];
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $page_size = 10;
        if (isset($_GET['page'])) {
            $offset = $page_size * $page;
        } else {
            $offset = 0;
        }
        $checkExam = SubjectCategory::model()->findByPk($id);
        $subscriber_subject = SubscriberCheckTest::model()->findAllByAttributes(array('subscriber_id' => $user_id, 'class_id' => $checkExam->class_id, 'subject_id' => $id), 'point >=18');
        if (count($subscriber_subject) == 0) {
            $this->redirect(Yii::app()->homeurl . '/questionTest/startTest?subjectId=' . $id . '&classId=' . $checkExam->class_id);
            return;
        }
        $query = "select * from question where status = 1 and category_id = $id order by modify_date desc limit $offset, $page_size";
//            $query = "select *, qm.question_id, q.id from question q join question_image qm on qm.question_id = q.id order by q.modify_date desc";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $question = $command->queryAll();
//        echo "<pre>";
//        print_r($question);
//        die;
        for ($i = 0; $i < count($question); $i++) {
            $question_id = $question[$i]['id'];
            $class_id = $question[$i]['class_id'];
            $category_id = $question[$i]['category_id'];
            $subcriber_id = $question[$i]['subscriber_id'];
            if (!Yii::app()->session['user_id']) {
                $checkLike = 0;
                $question[$i]['check_like'] = 0;
            } else {
                $checkLike = Like::model()->findByAttributes(
                        array(
                            'question_id' => $question_id,
                            'subscriber_id' => Yii::app()->session['user_id']
                        )
                );
                if (count($checkLike) > 0) {
                    $question[$i]['check_like'] = 1;
                } else {
                    $question[$i]['check_like'] = 0;
                }
            }
            $questionImage = QuestionImage::model()->findByAttributes(array('question_id' => $question_id, 'status' => 1));
            $question[$i]['base_url'] = $questionImage['base_url'];
            $class_name = Class1::model()->findByAttributes(array('id' => $class_id, 'status' => 1));
            $subjectCategory = SubjectCategory::model()->findByAttributes(array('id' => $category_id, 'status' => 1));
            $question[$i]['class_name'] = $class_name['class_name'];
            $question[$i]['subject_name'] = $subjectCategory['subject_name'];
            $Subcriber = Subscriber::model()->findByPk($subcriber_id);
            $level = Level::model()->findByPk($question[$i]['level_id']);
            if ($Subcriber['url_avatar'] != null) {
                if ($Subcriber['password'] == 'faccebook' || $Subcriber['password'] == 'Google') {
                    $url_avatar = $Subcriber['url_avatar'];
                } else {
                    $url_avatar = IPSERVER . $Subcriber['url_avatar'];
                }
//                $url_avatar = IPSERVER . $Subcriber['url_avatar'];
            } else {
                $url_avatar = '';
            }
            $question[$i]['subscriber_name'] = $Subcriber['lastname'] . ' ' . $Subcriber['firstname'];
            $question[$i]['sub_id'] = $Subcriber['id'];
            $question[$i]['url_avatar'] = $url_avatar;
            $question[$i]['level'] = $level->name;
        }
//        echo "<pre>";
//        print_r($question);
//        die;
        $this->render('question/questionSubject', array(
            'questions' => $question,
            'subjectId' => $id,
        ));
    }

    public function checkTheeQuestion($sub_name, $question, $transaction, $fcoin) {
        $time = date('Y-m-d H:i:s');
        Yii::log("\n IDDDDDDDDDDDDDDDDDDDDDDDDDD: APPPPPPPPPPP" . $question->type);
        $sub_name->save();
        $question->type = 2;
        $transaction->status = 1;
        $transaction->save();
        $question->save();
        return $question;
    }

    function actionUnholdQuestion() {
        $questionId = $_POST['questionId'];
        $user_id = $_POST['user_id'];
        $time = time();
        $result = HoldQuestion::model()->findByAttributes(array('question_id' => $questionId, 'subscriber_id' => $user_id), "end_time > $time");
        if ($user_id == -1) {
            echo 0;
            die;
        }
        $result->end_time = $time;
        $result->save();
        echo 1;
        die;
    }

    function ak_img_resize($target, $newcopy, $w, $h, $ext) {
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
        Yii::log('--------------5--------------' . $ext);
        Yii::log('--------------6--------------' . $target);
        if ($ext == "image/gif") {
            $img = imagecreatefromgif($target);
        } else if ($ext == "image/png") {
            Yii::log('--------------7--------------' . $target);
            $img = imageCreateFromPng($target);
        } else {
            Yii::log('--------------7--------------' . $target);
            $img = imageCreateFromJpeg($target);
        }
        Yii::log('--------------8--------------');
        $tci = imagecreatetruecolor($w, $h);
        Yii::log('--------------9--------------');
        // imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
        imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
        Yii::log('--------------10--------------');
        imagejpeg($tci, $newcopy, 80);
    }

    public function actionChangeLevel() {
        $level_id = $_POST['level_id'];
        $user_id = $_POST['user_id'];
        $question_id = $_POST['question_id'];
        $time = date('Y-m-d H:i:s');
        $level_max = $level_id + 1;
        if (!Level::model()->exists('id=' . $level_max)) {
            echo FAIL_SERVER;
            die;
        }
        $Changelevel = ChangeLevel::model()->findByAttributes(array('level_id' => $level_id, 'question_id' => $question_id, 'status' => 1));
        if ($Changelevel != null) {
            echo FAIL_MONEY;
            die;
        }
        $Changelevel = new ChangeLevel();
        $Changelevel->subscriber_id = $user_id;
        $Changelevel->question_id = $question_id;
        $Changelevel->level_id = $level_id;
        $Changelevel->created_date = $time;

        $question = Question::model()->findByPk($question_id);
        $sub_question = Subscriber::model()->findByPk($question->subscriber_id);
        $notification = new Subscriber();
        $content = 'Câu hỏi của bạn vượt mức độ, bạn có muốn tăng mức độ không';
        if (count($sub_question) > 0) {
            if ($sub_question->device_token != null || $sub_question->device_token != '') {
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
                        'Type' => 5,
                        'QuestionId' => $question_id,
                    );
                    $notification->send_notification($registatoin_ids, $message);
                }
            }
        }

        $Changelevel->save();
        echo SUCCEED;
        die;
    }

    public function actionSuccessLevel() {
        $level_id = $_POST['level_id'];
        $level_id_new = $_POST['level_id_new'];
        $question_id = $_POST['question_id'];
        $user_id = $_POST['user_id'];
        $time = date('Y-m-d H:i:s');
        if ($level_id >= $level_id_new) {
            echo LEVEL_NOT_EXIT;
            die;
        }
        $level = Level::model()->findByPk($level_id);
        $level_new = Level::model()->findByPk($level_id_new);
        $coin = $level_new->fcoin - $level->fcoin;
        $answer = Answer::model()->findByAttributes(array('question_id' => $question_id));
        if ($answer != null) {
            echo FAIL_MONEY;
            die;
        }
        $Changelevel = ChangeLevel::model()->findByAttributes(array('level_id' => $level_id, 'question_id' => $question_id, 'status' => 1));
        if ($Changelevel == null) {
            echo FAIL_MONEY;
            die;
        }
        $subscriber = Subscriber::model()->findByPk($user_id);
        if ($subscriber->fcoin < $coin) {
            echo CARD_ONCASH_MONEY;
            die;
        }
        $Changelevel->status = 3;
        $Changelevel->save();
        $question = Question::model()->findByPk($question_id);
        $question->modify_date = $time;
        $question->level_id = $level_id_new;
        $question->save();
        $subscriber->fcoin -= $coin;
        $subscriber->save();
    }

    public function actionLevelFail() {
        $level_id = $_POST['level_id'];
        $question_id = $_POST['question_id'];
        $user_id = $_POST['user_id'];
        $time = date('Y-m-d H:i:s');
        $answer = Answer::model()->findByAttributes(array('question_id' => $question_id));
        if ($answer != null) {
            echo FAIL_MONEY;
            die;
        }
        $Changelevel = ChangeLevel::model()->findByAttributes(array('level_id' => $level_id, 'question_id' => $question_id, 'status' => 1));
        if ($Changelevel == null) {
            echo FAIL_MONEY;
            die;
        }
        $Changelevel->status = 2;
        $Changelevel->save();
    }

    public function promitionFreeGold($question, $transaction, $user_id) {
        $goldTime = GoldTime::model()->findByAttributes(array('subscriber_id' => $user_id, 'type' => 1));
        if ($goldTime != null) {
            $goldTime->times += 1;
        } else {
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

    public function FreeQuestion($question, $transaction, $user_id) {
        Yii::log("\n IDDDDDDDDDDDDDDDDDDDDDDDDDD: " . $user_id);
        $goldTime = PromotionFreeContent::model()->findByAttributes(array('subscriber_id' => $user_id));
        if ($goldTime != null) {
            Yii::log("\n case 1: ");
            $goldTime->total += 1;
            $goldTime->save();
            Yii::log("\n IDDDDDDDDDDDDDDDDDDDDDDDDDD: " . $goldTime->total);
        }
        $question->type = 1; //Câu hỏi free
        $question->level_id = 1; //Câu hỏi free
        $transaction->status = 1;
        $transaction->cost = 0;
        $transaction->save();
        $question->save();
        return $question;
    }

    public function actionViewUnit() {
        $uid = $_POST['uid'];
        $questionID = $_POST['questionID'];
        $subscriber = Subscriber::model()->findByPk($uid);
        $subscriber->fcoin -= 1;
        $subscriber->save();
        return;
    }

    public function actionFail() {
        $this->titlePage = 'Câu hỏi';
        $id = $_REQUEST['id'];
        if (!isset($id)) {
            $this->redirect(Yii::app()->homeurl);
        }
        $questionCheck = Question::model()->findByPk($id);
        if ($questionCheck == null || $questionCheck->status == 9 || $questionCheck->status == 10 || $questionCheck->status == 11 || $questionCheck->status == 12) {
            $this->redirect(Yii::app()->homeurl);
        }
        if (!Yii::app()->session['user_id']) {
            $this->redirect(Yii::app()->homeurl . '/account');
        }
        $checkSubject = SubscriberCheckTest::model()->findByAttributes(array('subject_id' => $questionCheck->category_id), 'point >= 18');
        if ($checkSubject == null && $this->userName->type == 2) {
            $this->redirect(Yii::app()->homeurl);
        }
        $query = "select *, qm.question_id, q.id, q.status as status_q from question q join question_image qm on qm.question_id = q.id where q.id = $id";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $question = $command->queryRow();
        $class_id = $question['class_id'];
        $category_id = $question['category_id'];
        $subcriber_id = $question['subscriber_id'];
        if (!Yii::app()->session['user_id']) {
            $checkLike = 0;
            $user_id = null;
            $question['check_like'] = 0;
        } else {
            $user_id = Yii::app()->session['user_id'];
            $checkLike = Like::model()->findByAttributes(
                    array(
                        'question_id' => $id,
                        'subscriber_id' => Yii::app()->session['user_id']
                    )
            );
            if (count($checkLike) > 0) {
                $question['check_like'] = 1;
            } else {
                $question['check_like'] = 0;
            }
        }
        $class_name = Class1::model()->findByAttributes(array('id' => $class_id, 'status' => 1));
        $level = Level::model()->findByPk($question['level_id']);
        $subjectCategory = SubjectCategory::model()->findByAttributes(array('id' => $category_id, 'status' => 1));
        $question['class_name'] = $class_name['class_name'];
        $question['subject_name'] = $subjectCategory['subject_name'];
        $Subcriber = Subscriber::model()->findByPk($subcriber_id);
        if ($Subcriber['url_avatar'] != null) {
//            $url_avatar = IPSERVER.$Subcriber['url_avatar'];
            if ($Subcriber['password'] == 'faccebook' || $Subcriber['password'] == 'Google') {
                $url_avatar = $Subcriber['url_avatar'];
            } else {
                $url_avatar = IPSERVER . $Subcriber['url_avatar'];
            }
        } else {
            $url_avatar = '';
        }
        $question['subscriber_name'] = $Subcriber['lastname'] . ' ' . $Subcriber['firstname'];
        $question['sub_id'] = $Subcriber['id'];
        $question['url_avatar'] = $url_avatar;
        //answer
//        $query = "select *, ai.answer_id, a.id, a.question_id, ai.status, ai.title from answer a join answer_image ai on ai.answer_id = a.id where a.question_id = $id and ai.status <> 4 order by a.id desc";
        $query = "select * from answer where question_id = $id and status =4 order by id desc";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($query);
        $answer = $command->queryRow();
//        echo '<pre>';print_r($answer);die;
        $success = '';
        $checkLike = '';
        $subUser = '';
        $reply = '';
        $arrHoldQuestion = '';
        $url_images = array();
        $subUser = Subscriber::model()->findByPk($answer['subscriber_id']);
        $answer_id = $answer['id'];
        if ($user_id != null) {
            if ($answer != '') {
                $query = "select * from answer_image where answer_id = $answer_id order by id asc";
                $image = AnswerImage::model()->findAllBySql($query);
                //
                for ($j = 0; $j < count($image); $j++) {
                    $url_images[$j]['images'] = IPSERVER . $image[$j]['base_url'];
                    if ($image[$j]['width'] != null) {
                        $url_images[$j]['width'] = $image[$j]['width'];
                    } else {
                        $url_images[$j]['width'] = 0;
                    }
                    if ($image[$j]['height'] != null) {
                        $url_images[$j]['height'] = $image[$j]['height'];
                    } else {
                        $url_images[$j]['height'] = 0;
                    }
                }
                $answer['url_images'] = $url_images;
                $success = 5;
            }
            $time = time();
            $criteria = new CDbCriteria;
            $criteria->condition = "end_time > $time";
            $criteria->compare('question_id', $id);
            $arrHoldQuestion = HoldQuestion::model()->findAll($criteria);
        } else {
            $answer['url_images'] = $url_images;
        }
        $this->render('question/detail', array('question' => $question, 'answer' => $answer, 'checkLike' => $checkLike, 'success' => $success, 'subUser' => $subUser, 'id' => $id, 'reply' => $reply, 'arrHoldQuestion' => $arrHoldQuestion, 'level' => $level));
    }

}

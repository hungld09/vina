<?php

    /* Netbean
     * By Hungld
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    class QuestionBankController extends Controller
    {

        public function beforeAction($action)
        {
//        if (strcasecmp($action->id, 'index') != 0) {
//            $sessionKey = isset(Yii::app()->session['session_key']) ? Yii::app()->session['session_key'] : null;
//            if ($sessionKey == null) {
//                $this->redirect(Yii::app()->homeurl);
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

        public function actionIndex()
        {
//        $this->layout = 'ddd';
//            $model = new QuestionLib();

            if ($this->detect->isTablet() || $this->detect->isMobile()) {
                $this->titlePage = 'Tìm kiếm';
            } else {
                $this->titlePage = 'Thư viện lời giải';
            }

            $title      = !empty($_GET['namequestion']) ? $_GET['namequestion'] : '';
            $class_id   = isset($_GET['class']) ? $_GET['class'] : 0;
            $subject_id = isset($_GET['subject']) ? $_GET['subject'] : 0;
            $chapter_id = isset($_GET['chapter']) ? $_GET['chapter'] : 0;
            $unit_id    = !empty($_GET['unit']) ? $_GET['unit'] : 0;
            $tag_id     = isset($_GET['QuestionLib']['questionTag']) ? $_GET['QuestionLib']['questionTag'] : 0;
            $page       = isset($_GET['page']) ? $_GET['page'] : 1;

            $page_size = 10;
            if (isset($_GET['page'])) {
                $offset = $page_size * $page;
            } else {
                $offset = 0;
            }

//                $arTagId      = explode(',', $tag_id);
//                $arQuestionId = array();
//                foreach ($arTagId as $item) {
//                    $mappingQuesTag = TagQuestionMapping::model()->findAllByAttributes(array('tag_id' => $item));
//                    if (count($mappingQuesTag) > 0) {
//                        foreach ($mappingQuesTag as $ite) {
//                            $arQuestionId[] = $ite->question_id;
//                        }
//                    }
//                }

            $criteria = new CDbCriteria;

            if ($class_id > 0 && $subject_id > 0) {
                $criteria->compare('class_id', $class_id);
                $criteria->compare('subject_id', $subject_id);

                if ($chapter_id > 0) {
                    $criteria->compare('chapter_id', $chapter_id);
                }

                if (trim($title) != '') {
                    $title = CVietnameseTools::removeSigns2($title);
                    $criteria->compare('question_ascii', trim($title), TRUE);
                }

                if ($unit_id > 0) {
                    $criteria->compare('bai_id', $unit_id);
                }
            }

//                if (count($arQuestionId) > 0) {
//                    $criteria->addInCondition("id", $arQuestionId);
//                }

            $criteria->addCondition("status != 2");

//            $criteria->compare('status',1);
//            $criteria->compare('status', 0, false, 'OR');
            $criteria->limit  = $page_size;
            $criteria->offset = $offset;

//            $result = QuestionLib::model()->findAll($criteria);

            $arr_option['criteria'] = $criteria;
            if ($page_size) {
                $arr_option['pagination'] = array(
                    'pageSize' => $page_size,
                    'pageVar'  => 'page',
                    'route'    => 'questionBank/index'
                );
            } else {
                $arr_option['pagination'] = FALSE;
            }

            $result = new CActiveDataProvider('QuestionLib', $arr_option);

//                $subscriber = Subscriber::model()->findByPk(Yii::app()->session['user_id']);
            $class = Class1::model()->findAllByAttributes(array('status' => 1), 'id > 6');

            $this->render('questionBank/index', array(
                'result'     => $result,
//                    'subscriber' => $subscriber,
                'class_id'   => $class_id,
                'subject_id' => $subject_id,
                'chapter_id' => $chapter_id,
                'unit_id'    => $unit_id,
                'title'      => $title,
                'class'      => $class,
//                'model'      => $model
            ));
        }

        public function actionLoadSubject()
        {
            $class_id   = $_POST['class_id'];
            $query      = "select * from subject_category where class_id = $class_id";
            $connection = Yii::app()->db2;
            $command    = $connection->createCommand($query);
            $subject    = $command->queryAll();
            $html       = '';
            if (count($subject) > 0) {
                $html .= '<option value="0">Chọn Môn</option>';
                for ($i = 0; $i < count($subject); $i++) {
                    $selected = '';
                    if (isset($_POST['subject_id']) && $_POST['subject_id'] == $subject[$i]['id'])
                        $selected = ' selected=""';
                    $html .= '<option value="' . $subject[$i]['id'] . '"' . $selected . '>' . $subject[$i]['subject_name'] . '</option>';
                }
            } else {
                $html .= '<option value="0">Chưa cập nhật</option>';
            }
            echo $html;
        }

        public function actionLoadChapter()
        {
            $subject_id = $_POST['subject_id'];
            $class_id   = $_POST['class_id'];
            $query      = "select id, chapter_name, class_id, subject_id from chapter where class_id = $class_id and subject_id = $subject_id";
            $connection = Yii::app()->db2;
            $command    = $connection->createCommand($query);
            $chapter    = $command->queryAll();
            $html       = '';
            if (count($chapter) > 0) {
                $html .= '<option value="0">Chọn Chương</option>';
                for ($i = 0; $i < count($chapter); $i++) {
                    $selected = '';
                    if (isset($_POST['chapter_id']) && $_POST['chapter_id'] == $chapter[$i]['id'])
                        $selected = ' selected=""';
                    $html .= '<option value="' . $chapter[$i]['id'] . '"' . $selected . '>' . $chapter[$i]['chapter_name'] . '</option>';
                }
            } else {
                $html .= '<option value="0">Chưa cập nhật</option>';
            }
            echo $html;
        }

        public function actionLoadUnit()
        {
            $subject_id = $_POST['subject_id'];
            $class_id   = $_POST['class_id'];
            $chapter_id = $_POST['chapter_id'];
            $query      = 'select id, title, class_id, subject_id, chapter_id from bai where class_id = ' . $class_id . ' and subject_id = ' . $subject_id . ' and chapter_id = ' . $chapter_id . '';
            $connection = Yii::app()->db2;
            $command    = $connection->createCommand($query);
            $unit       = $command->queryAll();
            $html       = '';
            if (count($unit) > 0) {
                $html .= '<option value="0">Chọn bài</option>';
                for ($i = 0; $i < count($unit); $i++) {
                    $selected = '';
                    if (isset($_POST['unit_id']) && $_POST['unit_id'] == $unit[$i]['id'])
                        $selected = ' selected=""';
                    $html .= '<option value="' . $unit[$i]['id'] . '"' . $selected . '>' . $unit[$i]['title'] . '</option>';
                }
            } else {
                $html .= '<option value="0">Chưa cập nhật</option>';
            }
            echo $html;
        }

        public function actionList()
        {
            $this->titlePage = 'Kết quả tìm kiếm';

            $title      = !empty($_GET['namequestion']) ? $_GET['namequestion'] : '';
            $class_id   = isset($_GET['class']) ? $_GET['class'] : 0;
            $subject_id = isset($_GET['subject']) ? $_GET['subject'] : 0;
            $chapter_id = isset($_GET['chapter']) ? $_GET['chapter'] : 0;
            $unit_id    = !empty($_GET['unit']) ? $_GET['unit'] : 0;
//            $tag_id     = isset($_GET['QuestionLib']['questionTag']) ? $_GET['QuestionLib']['questionTag'] : 0;
            $page = isset($_GET['page']) ? $_GET['page'] : 1;

            $page_size = 10;
            if (isset($_GET['page'])) {
                $offset = $page_size * $page;
            } else {
                $offset = 0;
            }

            if ($class_id == 0 || $subject_id == 0) {
                $result     = array();
                $subscriber = Subscriber::model()->findByPk(Yii::app()->session['user_id']);
                $this->render('questionBank/list', array('result' => $result, 'subscriber' => $subscriber));
            } else {
//                $arTagId      = explode(',', $tag_id);
//                $arQuestionId = array();
//                foreach ($arTagId as $item) {
//                    $mappingQuesTag = TagQuestionMapping::model()->findAllByAttributes(array('tag_id' => $item));
//                    if (count($mappingQuesTag) > 0) {
//                        foreach ($mappingQuesTag as $ite) {
//                            $arQuestionId[] = $ite->question_id;
//                        }
//                    }
//                }

                $criteria = new CDbCriteria;

                if ($class_id > 0 && $subject_id > 0) {
                    $criteria->compare('class_id', $class_id);
                    $criteria->compare('subject_id', $subject_id);

                    if ($chapter_id > 0) {
                        $criteria->compare('chapter_id', $chapter_id);
                    }

                    if (trim($title) != '') {
                        $title = CVietnameseTools::removeSigns2($title);
                        $criteria->compare('question_ascii', trim($title), TRUE);
                    }
                    if ($unit_id > 0) {
                        $criteria->compare('bai_id', $unit_id);
                    }
                }

//                if (count($arQuestionId) > 0) {
//                    $criteria->addInCondition("id", $arQuestionId);
//                }

                $criteria->addCondition("status != 2");

//            $criteria->compare('status',1);
//            $criteria->compare('status', 0, false, 'OR');
                $criteria->limit  = $page_size;
                $criteria->offset = $offset;

                $result = QuestionLib::model()->findAll($criteria);
//                $subscriber       = Subscriber::model()->findByPk(Yii::app()->session['user_id']);

                $this->render('questionBank/list', array(
                    'result'     => $result,
//                    'subscriber' => $subscriber,
                    'class_id'   => $class_id,
                    'subject_id' => $subject_id,
                    'chapter_id' => $chapter_id,
                    'unit_id'    => $unit_id,
                    'title'      => $title,
                ));
            }
        }

        public function actionView($id)
        {
            $this->titlePage = 'Chi tiết';
            if (!isset($id)) {
                $this->redirect(Yii::app()->homeurl);
            }
//        $subscriber = Subscriber::model()->findByPk(Yii::app()->session['user_id']);
//        if($subscriber->fcoin < 5){
//            $this->render('questionBank/detail', array('result'=>$result));
//        }
            $checkReg = $this->checkUserService();
            if (count($checkReg) == 0) {
                $subscriber = Subscriber::model()->findByPk(Yii::app()->session['user_id']);
                $subscriber->fcoin -= 5;
                $subscriber->save();
            }
            $result = QuestionLib::model()->findByPk($id);
            $this->render('questionBank/detail', array('result' => $result));
        }

        public function actionLists()
        {

            $q = $_GET['q'];

            $criteria = new CDbCriteria;
            $criteria->compare('name', $q, TRUE);
            $criteria->limit  = 10;
            $criteria->offset = 0;
            $rows             = QuestionTag::model()->findAll($criteria);
            $result           = array();
            foreach ($rows as $row) {
                $item         = array();
                $item['id']   = $row->id;
                $item['name'] = $row->name;
                $result[]     = $item;
            }

            echo $_GET['callback'] . "(";
            echo CJSON::encode($result);
            echo ")";
        }

        private function checkUserService($service_id = NULL)
        {
            $time     = date('Y-m-d H:i:s');
            $criteria = new CDbCriteria;
            if ($service_id == NULL) {
                $criteria->condition = "expiry_date > '$time'";
            } else {
                $criteria->condition = "service_id = $service_id and expiry_date > '$time'";
            }
            $criteria->compare('is_active', 1);
            $criteria->compare('subscriber_id', Yii::app()->session['user_id']);
            $usingService = ServiceSubscriberMapping::model()->findAll($criteria);

            return $usingService;
        }

        public function actionLoaditem()
        {
            $title      = $_POST['title'];
            $click      = $_POST['click'];
            $class_id   = isset($_POST['class_id']) ? $_POST['class_id'] : 0;
            $subject_id = isset($_POST['subject_id']) ? $_POST['subject_id'] : 0;
            $chapter_id = isset($_POST['chapter_id']) ? $_POST['chapter_id'] : 0;
            $unit_id    = !empty($_POST['unit_id']) ? $_POST['unit_id'] : 0;

            $page      = $click;
            $page_size = 10;
            $offset    = $page_size * $page;

            $criteria = new CDbCriteria;

            if ($class_id > 0 && $subject_id > 0) {
                $criteria->compare('class_id', $class_id);
                $criteria->compare('subject_id', $subject_id);

                if ($chapter_id > 0) {
                    $criteria->compare('chapter_id', $chapter_id);
                }

                if (trim($title) != '') {
                    $title = CVietnameseTools::removeSigns2($title);
                    $criteria->compare('question_ascii', trim($title), TRUE);
                }

                if ($unit_id > 0) {
                    $criteria->compare('bai_id', $unit_id);
                }
            }


            $criteria->addCondition("status != 2");

//            $criteria->compare('status',1);
//            $criteria->compare('status', 0, false, 'OR');
            $criteria->limit  = $page_size;
            $criteria->offset = $offset;
            $result           = QuestionLib::model()->findAll($criteria);

            if (count($result))
                return $this->renderPartial('questionBank/_getItem', array(
                    'result'     => $result,
                    'class_id'   => $class_id,
                    'subject_id' => $subject_id,
                    'chapter_id' => $chapter_id,
                    'unit_id'    => $unit_id,
                    'title'      => $title,
                ));

            return FALSE;
        }

    }

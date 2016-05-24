<?php

    class SiteController extends Controller
    {
        private $pageSize = 10;
        const sessionTimeoutSeconds = 86400;

        /**
         * Declares class-based actions.1
         */
        public function actions()
        {
            return array(
                // captcha action renders the CAPTCHA image displayed on the contact page
                'captcha' => array(
                    'class'     => 'CCaptchaAction',
                    'backColor' => 0xFFFFFF,
                ),
                // page action renders "static" pages stored under 'protected/views/site/pages'
                // They can be accessed via: index.php?r=site/page&view=FileName
                'page'    => array(
                    'class' => 'CViewAction',
                ),
            );
        }

        public function beforeAction($action)
        {

            // Check only when the user is logged in
            if (!Yii::app()->user->isGuest || Yii::app()->session['user_id']) {
                if (yii::app()->user->getState('userSessionTimeout') < time()) {
                    // timeout
                    Yii::app()->user->logout();
                    Yii::app()->session->clear();
                    Yii::app()->session->destroy();
                    $this->redirect(Yii::app()->homeurl);
                } else {
                    yii::app()->user->setState('userSessionTimeout', time() + self::sessionTimeoutSeconds);

                    return TRUE;
                }
            } else {
                return TRUE;
            }

            return parent::beforeAction($action);
        }

        public function actionConfirmRegister()
        {
            if (isset($_GET['sessionkey'])) {
                $sessionkey      = str_replace(' ', '+', $_GET['sessionkey']);
                $time            = time();
                $confirmRegister = ConfirmRegister::model()->findByAttributes(array('sessionkey' => $sessionkey), "end_time > $time");
                if ($confirmRegister != NULL) {
                    $subscriber = Subscriber::model()->findByPk($confirmRegister->subscriber_id);
                    if ($subscriber->type == 1) {
                        $subscriber->status = 1;// xac nhan trang thai ok
                    } else {
                        $subscriber->status = 4; // trang thai chua lam bai test he thong
                    }
                    if ($subscriber->save()) {
                        $checkPartner        = Partner::model()->findByAttributes(array('provider' => $subscriber->partner_id), 'id not in (2,3)');
                        $PromotionSubscriber = new PromotionFreeContent();
                        if ($subscriber->partner_id == 'net2e' && $subscriber->type == 1 && $subscriber->password != 'faccebook') {
                            $PromotionSubscriber->type = 2;
                        } else if ($checkPartner != NULL && $subscriber->password != 'faccebook') {
                            $PromotionSubscriber->type = 1;
                        } else {
                            $PromotionSubscriber->type = 0;
                        }
                        $PromotionSubscriber->subscriber_id = $subscriber->id;
                        $PromotionSubscriber->total         = 0;
                        $PromotionSubscriber->created_date  = date('Y-m-d H:i:s');
                        $PromotionSubscriber->save();
                        $confirmRegister->end_time = $time;
                        $confirmRegister->save();
                        if ($subscriber->type == 1) {
                            echo 'Tài khoản học sinh xác minh thành công. Bấm vào đây để quay về <a href="https://www.hocde.vn/">https://www.hocde.vn/</a>';
                            die;
                        } else {
                            echo 'Tài khoản thầy giáo xác minh thành công. Bấm vào đây để làm bài kiểm tra chuyên môn <a href="https://www.hocde.vn/">https://www.hocde.vn/</a>';
                            die;
                        }
                    }
                } else {
                    echo 'Tài khoản xác minh không tồn tại hoặc đã quá thời gian xác minh xin mời đăng ký lại!';
                    die;
                }

            } else {
                echo 'Tài khoản xác minh không tồn tại!';
                die;
            }
        }

        /**
         * This is the default 'index' action that is invoked
         * when an action is not explicitly requested by users.
         */
        public function actionIndex()
        {
            $this->titlePage = 'Trang chủ';

            if ($this->detect->isTablet() || $this->detect->isMobile()) {
                $class = Class1::model()->findAllByAttributes(array('status' => 1), 'id > 6');

                $this->render('site/index', array(
                    'class' => $class,
                ));
            } else {
                $this->render('site/index', array());
            }
        }

        /**
         * This is the action to handle external exceptions.
         */
        public function actionError()
        {
            if ($error = Yii::app()->errorHandler->error) {
                if (Yii::app()->request->isAjaxRequest)
                    echo $error['message'];
                else
                    $this->render('site/error', $error);
            }
        }

        /**
         * Displays the contact page
         */
        public function actionContact()
        {
            $model = new ContactForm;
            if (isset($_POST['ContactForm'])) {
                $model->attributes = $_POST['ContactForm'];
                if ($model->validate()) {
                    $name    = '=?UTF-8?B?' . base64_encode($model->name) . '?=';
                    $subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';
                    $headers = "From: $name <{$model->email}>\r\n" .
                        "Reply-To: {$model->email}\r\n" .
                        "MIME-Version: 1.0\r\n" .
                        "Content-type: text/plain; charset=UTF-8";

                    mail(Yii::app()->params['adminEmail'], $subject, $model->body, $headers);
                    Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                    $this->refresh();
                }
            }
            $this->render('contact', array('model' => $model));
        }

        /**
         * Displays the login page
         */
        public function actionLogin()
        {
            $model = new LoginForm;

            // if it is ajax validation request
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }

            // collect user input data
            if (isset($_POST['LoginForm'])) {
                $model->attributes = $_POST['LoginForm'];
                // validate user input and redirect to the previous page if valid
                if ($model->validate() && $model->login())
                    $this->redirect(Yii::app()->user->returnUrl);
            }
            // display the login form
            $this->render('login', array('model' => $model));
        }

        /**
         * Logs out the current user and redirect to homepage.
         */
        public function actionLogout()
        {
            Yii::app()->user->logout();
            $this->redirect(Yii::app()->homeUrl);
        }

        public function actionClause()
        {

            $this->render('site/clause');
        }

        public function actionRegisterLogin()
        {

            $this->render('site/registerLogin');
        }

        public function actionSearchAnswer()
        {

            $this->render('site/searchAnswer');
        }

        public function actionPostQuestion()
        {

            $this->render('site/postQuestion');
        }

        public function actionGetAnswer()
        {

            $this->render('site/getAnswer');
        }

        public function actionForgotPassword()
        {

            $this->render('site/forgotPassword');
        }

        public function actionRechargeCard()
        {

            $this->render('site/rechargeCard');
        }

        public function actionPc_laptop()
        {

            $this->render('site/pc_laptop');
        }

        public function actionTopical()
        {
            if (isset($_GET['id'])) {
                $title   = $_GET['title'];
                $id      = $_GET['id'];
                $topical = Topical::model()->findByAttributes(array('title_code' => $id));
            } else {
                echo 'Sai đường dẫn! xin cảm ơn';
                die;
            }

            $this->render('site/topical', array(
                'topical' => $topical,
            ));
        }

        public function actionCategory()
        {
            $page      = isset($_GET['page']) && preg_match("/^\d+$/", $_GET['page']) ? $_GET['page'] : 0;
            $page      = $page > 0 ? $page - 1 : 0;
            $pager     = array();
            $page_size = 6;

            if (isset($_GET['id'])) {
                $id       = $_GET['id'];
                $category = CategoryTopical::model()->findByAttributes(array('image_url' => $id));
//            $topicals = Topical::model()->findAllByAttributes(array('category_id' => $id));

                $criteria = new CDbCriteria();
                $criteria->compare('category_id', $category->id);
                $totalTopicals    = Topical::model()->findAll($criteria);
                $criteria->limit  = $page_size;
                $criteria->offset = $page_size * $page;
                $criteria->order  = 'id DESC';
                $topicals         = Topical::model()->findAll($criteria);

                $total_result = count($totalTopicals);
                $total_page   = ($total_result - ($total_result % $page_size)) / $page_size + (($total_result % $page_size === 0) ? 0 : 1);
                $a            = array('page_number' => $page, 'page_size' => 8, 'total_result' => $total_result, 'total_page' => $total_page);
                foreach (array('total_result', 'page_number', 'page_size', 'total_page') as $e) {
                    if (array_key_exists($e, $a))
                        $pager[$e] = $a[$e];
                }
            } else {
                echo 'Sai đường dẫn! xin cảm ơn';
                die;
            }

            $this->render('site/category', array(
                'topicals'     => $topicals,
                'category'     => $category,
                'pager'        => $pager,
                'total_result' => $total_result
            ));
        }

        public function actionPolicy()
        {
            $this->layout = '123';
            $this->render('site/policy');
        }

        public function actionPolicy_en()
        {
            $this->layout = '123';
            $this->render('site/policy_en');
        }

        public function actionProvision()
        {
            $this->layout = '123';
            $this->render('site/provision');
        }

        public function actionProvision_en()
        {
            $this->layout = '123';
            $this->render('site/provision_en');
        }
    }

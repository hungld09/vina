<?php

    class VideosController extends Controller
    {

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

        public function actionIndex()
        {
            $this->titlePage = 'Clip bài giảng';

            $criteria = new CDbCriteria;
            $criteria->addCondition('status = 1');
            $criteria->limit = 5;
            $data            = Videos::model()->findAll($criteria);

            $this->render('videos/index', array('data' => $data));
        }
    }

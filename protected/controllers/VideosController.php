<?php

class VideosController extends Controller {

    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionIndex() {
        $this->titlePage = 'Clip bài giảng';

        $criteria = new CDbCriteria;
        $criteria->addCondition('status = 1');
        $criteria->limit = 5;
        $data = Videos::model()->findAll($criteria);

        $this->render('videos/index', array('data' => $data));
    }

    public function actionList() {
        $this->titlePage = 'Clip bài giảng';

        $criteria = new CDbCriteria;
        $criteria->addCondition('status = 1');
        $criteria->limit = 5;
        $data = Videos::model()->findAll($criteria);
        $class = Class1::model()->findAllByAttributes(array('status' => 1),'id > 6');
        $this->render('videos/list', array('data' => $data, 'class'=>$class));
    }

    public function actionLoadSubject() {
        $class_id = $_POST['class_id'];
        $query = "select * from subject_category where class_id = $class_id";
        $connection = Yii::app()->db2;
        $command = $connection->createCommand($query);
        $subject = $command->queryAll();
        $html = '';
        if (count($subject) > 0) {
            $html .='<option value="-1">Chọn Môn</option>';
            for ($i = 0; $i < count($subject); $i++) {
                $html .='<option value="' . $subject[$i]['id'] . '">' . $subject[$i]['subject_name'] . '</option>';
            }
        } else {
            $html .='<option value="-1">Chưa cập nhật</option>';
        }
        echo $html;
    }
}

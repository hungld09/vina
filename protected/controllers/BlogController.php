<?php

class BlogController extends Controller {

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
        $this->titlePage = 'Tin tức và sự kiện';
        $cate = CategoryBlog::model()->findAll();
        $this->render('blog/index', array('category'=>$cate));
    }
    public function actionCate($id) {
        $this->titlePage = 'Tin tức và sự kiện';
        $results = Blog::model()->findAllByAttributes(array('category_id'=>$id));
        $this->render('blog/list', array('items'=>$results));
    }
    public function actionView($id) {
        $this->titlePage = 'Tin tức và sự kiện';
        $results = Blog::model()->findByPk($id);
        $this->render('blog/detail', array('item'=>$results));
    }

}

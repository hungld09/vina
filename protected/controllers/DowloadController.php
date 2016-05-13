<?php

class DowloadController extends Controller
{
    public function actionIndex(){
        $this->layout = 'main1';
        $this->render('dowload/index', array());
    }  
}
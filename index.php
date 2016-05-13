<?php
// change the following paths if necessary
$yii=dirname(__FILE__).'/../yii-1.1.14.f0fee9/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';
$project_const=dirname(__FILE__).'/protected/config/const.php';
// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once($project_const);
require_once($yii);
Yii::createWebApplication($config)->run();

<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

if (!isset($root_dir)) $root_dir = dirname(dirname(dirname(dirname(__FILE__))));
Yii::setPathOfAlias('protected', $root_dir . '/hocde.vn/protected/');

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'My Web Application',

    // preloading 'log' component
    'preload' => array('log'),

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.extensions.yii-mail.*',
	'application.extensions.*',
	'application.extensions.select2.*',
	'application.extensions.inc.*',
    ),

    'modules' => array(
        'CURL' => array(
            'class' => 'application.extensions.curl.Curl',
            //you can setup timeout,http_login,proxy,proxylogin,cookie, and setOPTIONS
        ),
        // uncomment the following to enable the Gii tool
        'api',
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'admin',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            //'ipFilters'=>array('127.0.1.1','::1'),
        ),
    ),

    // application components
    'components' => array(
        'yii-mail' => array(
            'class' => 'application.extensions.yii-mail.YiiMailMessage',
            'delivery' => 'php', //Will use the php mailing function.
            //May also be set to 'debug' to instead dump the contents of the email into the view
        ),
//             'cache'=>array(
//             		'class'=>'CApcCache',
//             ),

        'mail' => array(
            'class' => 'application.extensions.yii-mail.YiiMail',
            'transportType' => 'smtp',
            'transportOptions' => array(
                'host' => 'smtp.gmail.com',
                'encryption' => 'ssl',
                'username' => 'admin@socotec.vn',
                'password' => 'dangcanhnd',
                'port' => '465',
            ),
            'viewPath' => 'application.views.mail',
            'logging' => true,
            'dryRun' => false,
        ),
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
        ),
        // uncomment the following to enable URLs in path-format
        'urlManager' => array(
            'urlFormat' => 'path',
            'rules' => array(
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                'hs<hs:\w[A-Z a-z 0-9 _ -]+>'=>'account/channelHs2',
            ),
        ),
        /*'urlManager'=>array(
            'urlFormat'=>'path',
            'rules'=>array(
                'post/<id:\d+>/<title:.*?>'=>'post/view',
                'posts/<tag:.*?>'=>'post/index',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
        ),*/
        /*		'db'=>array(
                    'connectionString' => 'mysql:'.dirname(__FILE__).'/../data/testdrive.db',
                ),*/
        // uncomment the following to use a MySQL database
//		'db'=>array(
//			'connectionString' => 'mysql:host=localhost;dbname=hocde',
//			'emulatePrepare' => true,
//			'username' => 'root',
//			'password' => 'dangcanhnd',
//			'charset' => 'utf8',
////		),
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=vina',
            'class' => 'application.extensions.PHPPDO.CPdoDbConnection',
            'pdoClass' => 'PHPPDO',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '09091990',
            'charset' => 'utf8',
        ),
        'db2'=>array(
            'class'=>'CDbConnection',
            'connectionString' => 'mysql:host=123.30.200.84;dbname=hocde1',
            'emulatePrepare' => true,
            'username' => 'hocde',
            'password' => 'hocde@2015',
            'charset' => 'utf8',
        ),
	'db3'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=vina',
            'class' => 'application.extensions.PHPPDO.CPdoDbConnection',
            'pdoClass' => 'PHPPDO',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '09091990',
            'charset' => 'utf8',
        ),
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
//		'log'=>array(
//			'class'=>'CLogRouter',
//			'routes'=>array(
//				array(
//					'class'=>'CFileLogRoute',
//					'levels'=>'trace, info, error, warning',
//				),
//				// uncomment the following to show log messages on web pages
//				/*
//				array(
//					'class'=>'CWebLogRoute',
//				),
//				*/
//			),
//		),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info',
                    'maxFileSize' => 102400,
                    'maxLogFiles' => 20,
                ),
                // uncomment the following to show log messages on web pages

                //	array(
                //		'class'=>'CWebLogRoute',
                //	),

            ),
        ),
        'mobileDetect' => array(
            'class' => 'application.extensions.mobileDetect.MobileDetect'
        ),
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
        'voucher' => array(
            'partnerCode' => 'test',
            'password' => '123456',
            'secretKey' => 'test_sk',
        ),
        'chargingProxy' => array(
            'url' => 'http://10.1.10.86:8080/billing/billing', //chay that tren server
// 				'url'  => 'http://113.185.0.153:8080/ccgw/billing/billing', //chay tren may tinh
            'name' => 'hocde',
            'user' => 'hocde@2016',
            'pass' => 'hocde@2016',
        ),
        'chargingProxy_test' => array(
            'url' => 'http://10.1.10.173/vascmd/vasprovisioning/api', //test
// 				'url'  => 'http://113.185.0.153:8080/ccgw/billing/billing', //chay tren may tinh
            'name' => 'hocde',
            'user' => 'hocde@2016',
            'pass' => 'hocde@2016',
        ),
        'net2e' => array(
            'username' => 'hocde',
            'partnerCode' => 'hocde@vn',
            'password' => 'e27d61b7da2c27a6a6deb751e9970ff9',
            'partner_id' => '123.30.200.85',
        ),
        'ipRanges' => array(
            '127.0.0.0/16',
            '118.70.233.163/32',//IP SCT test
            '192.168.0.0/16',
            '113.185.0.0/18',
            '10.0.0.0/8',
            '172.16.30.11/32',
            '172.16.30.12/32',
            '37.228.104.0/21',
            '58.67.157.0/24',
            '59.151.95.128/25',
            '59.151.98.128/27',
            '59.151.106.224/27',
            '59.151.120.32/27',
            '80.84.1.0/24',
            '80.239.242.0/23',
            '82.145.208.0/20',
            '91.203.96.0/22',
            '116.58.209.36/27',
            '116.58.209.128/27',
            '141.0.8.0/21',
            '195.189.142.0/23',
            '203.81.19.0/24',
            '209.170.68.0/24',
            '217.212.230.0/23',
            '217.212.226.0/24',
            '185.26.180.0/22'
        ),
    ),
);

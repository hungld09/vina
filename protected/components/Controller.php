<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/column1';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     *///
    public $menu = array();
    public $detect;
    public $tablet = false;
    public $pageTitle;
    public $userName;
    public $titlePage;
    public $title = 'Hướng dẫn giải bài tập toán, lý, hóa online | Học Dễ OnEdu';
    public $description = 'Học dễ Onedu, Hoc de Onedu là ứng dụng hỗ trợ hướng dẫn giải bài tập toán, giải bài tập vật lý, giải bài tập hóa học online nhanh, hiệu quả nhất';
    public $keywords = 'học dễ, hoc de, học dễ onedu, hoc de onedu, giải bài tập toán, giải bài tập vật lý, giải bài tập hóa học, hướng dẫn giải bài tập, đáp án bài tập, để học tốt';
    //public $user_id ='';
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public static $ACCESS_VIA_WIFI = 1;
    public static $ACCESS_VIA_3G = 2;
    public $breadcrumbs = array();
    public $array_file = array();
    public $msisdn;
//    public $msisdn = '84911321055';
    public $id;
    public $subscriber;
    public $services;
    public $usingServices;
    public $accessType;
    public $user_session;

    public function __construct($id, $module) {
        $ipRanges = Yii::app()->params['ipRanges'];
        $clientIP = Yii::app()->request->getUserHostAddress();
        $found = false;
        $this->accessType = self::$ACCESS_VIA_WIFI;
        $clientIP = explode(',', $clientIP);
        //Detect access via 3G?
        foreach ($ipRanges as $range) {
            foreach ($clientIP as $item) {
                if (CUtils::cidrMatch($item, $range)) {
                    $found = true;
                    $this->accessType = self::$ACCESS_VIA_3G;
                    break;
                }
            }
        }

        //Get MSISDN via 3G
        if ($found) {
            $this->msisdn = "";
            $headers = getallheaders();
            if (isset($headers['MSISDN'])) {
                $this->msisdn = $headers['MSISDN'];
            } else if (isset($headers['msisdn'])) {
                $this->msisdn = $headers['msisdn'];
            } else if (isset($headers['X-Wap-MSISDN'])) {
                $this->msisdn = $headers['X-Wap-MSISDN'];
            } else if (isset($headers['X-WAP-MSISDN'])) {
                $this->msisdn = $headers['X-WAP-MSISDN'];
            }
        }
//        $this->msisdn = '84911321055';
        //Check xem dang nhap qua wifi ko?
        if ($this->msisdn == '') {
            $this->accessType = self::$ACCESS_VIA_WIFI;
//            $this->user_session = Yii::app()->user->getState('session');
//            $this->msisdn = (Yii::app()->user->getId() != NULL) ? Yii::app()->user->getId() : '';
//            //Verify account with session id
//            $subcriber_id = Subscriber::model()->findByAttributes(array('subscriber_number' => $this->msisdn));
//            if ($subcriber_id != NULL && $this->user_session != NULL) {
//                $subSession = SubscriberSession::model()->findByAttributes(array('session_id' => $this->user_session, 'subscriber_id' => $subcriber_id->id, 'status' => 1));
//                if ($subSession != NULL) {
//                    $found = true;
//                } else {
//                    $this->msisdn = '';
//                }
//            } else {
//                $this->msisdn = '';
//            }
        }
        $this->services = Service::model()->getService();
        if ($this->msisdn != '') {
            $subcriber= Subscriber::model()->findByAttributes(array('subscriber_number' => $this->msisdn));
//            $this->id = $subcriber->id;
            $this->usingServices = ServiceSubscriberMapping::model()->findByAttributes(array('is_active'=>1, 'subscriber_id'=>$this->id));
        }else{
            $this->usingServices = null;
        }
        //
        $this->detect = Yii::app()->mobileDetect;
        if ($this->detect->isTablet()) {
            $this->tablet = true;
            Yii::app()->theme = "advance";
        } else
        if ($this->detect->isMobile()) {
            if ($this->detect->version('Windows Phone')) {
                Yii::app()->theme = 'advance';
            } elseif ($this->detect->is('Opera')) {
                Yii::app()->theme = 'advance';
            } else if ($this->detect->is('AndroidOS')) {
                if ($this->detect->version('Android') < 3.0) {
                    Yii::app()->theme = 'advance';
                } else {
                    Yii::app()->theme = 'advance';
                }
            } else if ($this->detect->is('iOS')) {
                if ($this->detect->getIOSGrade() === 'B') {
                    Yii::app()->theme = 'advance';
                } else {
                    Yii::app()->theme = 'advance';
                }
            } else {
                if ($this->detect->mobileGrade() === 'A') {
                    Yii::app()->theme = 'advance';
                } else {
                    Yii::app()->theme = 'advance';
                }
            }
        } else {
            Yii::app()->theme = 'web';
        }
//        Yii::app()->theme = 'advance';
    }

}

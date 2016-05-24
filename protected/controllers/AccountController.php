<?php

class AccountController extends Controller
{
    const sessionTimeoutSeconds = 86400;

    /**
	 * Declares class-based actions.1
	 */
	public function actions()
	{
            return array(
                // captcha action renders the CAPTCHA image displayed on the contact page
                'captcha'=>array(
                        'class'=>'CCaptchaAction',
                        'backColor'=>0xFFFFFF,
                ),
                // page action renders "static" pages stored under 'protected/views/site/pages'
                // They can be accessed via: index.php?r=site/page&view=FileName
                'page'=>array(
                        'class'=>'CViewAction',
                ),
            );
	}

	public function beforeAction($action)
	{
		if (strcasecmp($action->id, 'channelHs2') != 0 && strcasecmp($action->id, 'channel1loginface') != 0 && strcasecmp($action->id, 'channelHs1') != 0 && strcasecmp($action->id, 'Channelloginface') != 0 && strcasecmp($action->id, 'channelHs') != 0 && strcasecmp($action->id, 'savepartner') != 0 && strcasecmp($action->id, 'partner') != 0 && strcasecmp($action->id, 'accountNet2e') != 0 && strcasecmp($action->id, 'loginGoogle') != 0 && strcasecmp($action->id, 'loginface') != 0 && strcasecmp($action->id, 'login') != 0 && strcasecmp($action->id, 'register') != 0 && strcasecmp($action->id, 'index')) {
			$sessionKey = isset(Yii::app()->session['session_key']) ? Yii::app()->session['session_key'] : null;
			if ($sessionKey == null) {
				$this->redirect(Yii::app()->homeurl);
			}
			$sessionKey = str_replace(' ', '+', $sessionKey);
			Yii::log("\n SessionKey: " . $sessionKey);
			if (!CUtils::checkAuthSessionKey($sessionKey)) {
                            Yii::app()->user->logout();
                            Yii::app()->session->clear();
                            Yii::app()->session->destroy();
                            Yii::app()->user->setFlash('responseToUser', 'Tài khoản Đã bị đăng nhập trên thiêt bị khác');
                            $this->redirect(Yii::app()->homeurl);
                            return false;
			}
		}
		return parent::beforeAction($action);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */

	public function actionIndex()
	{
//            $this->render('site/index', array(
//                'questions'=>$question
//            ));
            $this->layout = 'main1';
            $this->render('account/index', array());
	}
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}
    public function actionLogin(){
        if(isset($_POST['submit'])){
            $CUtils = new CUtils();
            $msisdn = isset($_POST['mobile']) ? $CUtils->validatorMobile($_POST['mobile']) : '';
            $pass = isset($_POST['password']) ? $_POST['password'] : '';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, 'http://10.1.10.47/otp/checkotp?msisdn=' . $msisdn . '&otptoken=' . $pass . '&servicename=MSTYLE');
            $response = curl_exec($ch);
            curl_close($ch);
            $result = explode('|', $response);
            if (count($result) > 1) {
                if ($result [0] > 0) {
//                    log_message('error', "checkotp fail!!");
                 /*   $myvar = array(
                        "result" => $result
                    );
                    echo json_encode($myvar);
*/
		Yii::app()->user->setFlash('responseToUser', 'Số điện thoại hoặc mật khẩu của bạn không hợp lệ');
                return $this->redirect(Yii::app()->homeurl . '/account/login');
                } else {
                    Yii::app()->session['msisdn'] = $msisdn;
//                    $sessionKey = CUtils::generateSessionKey($user->id);
                    Yii::app()->session['session_key'] = $sessionKey;
                    return $this->redirect(Yii::app()->homeurl.'/site');
                }
            } else { // else password wrong
              /*  $myvar = array(
                    "result" => 'password'
                );
                echo json_encode($myvar);
*/
		Yii::app()->user->setFlash('responseToUser', 'Số điện thoại hoặc mật khẩu của bạn không hợp lệ');
                return $this->redirect(Yii::app()->homeurl . '/account/login');
            }
        }else{
            $this->redirect(Yii::app()->homeurl.'/account');
        }
    }
    public function actionLogout(){
//        unset(Yii::app()->session['user_id']);
        //$this->user_id = '';
        session_unset();
        session_destroy();
    	Yii::app()->session->clear();
        Yii::app()->session->destroy();
        $this->redirect(Yii::app()->homeurl.'/account');
    }
    
    public function Register($transaction_id = '', $returnUrl = 'http://vhocde.vn/', $package = 'NGAY', $channel = 'WAP') {
        $transaction_id = self::makeRequestID();
        $request_id = $transaction_id;
        $returnUrl_encode = urlencode($returnUrl);
        $backUrl = $returnUrl;
        $backUrl_encode = urlencode($backUrl);
        $cp = "CP_DIGISOFT";
        $service = "MSTYLE";
        $packagename = $package;
        $request_datetime = date('YmdHis');
        $secure_pass = 'DIGISOFT@022016';
        $secure_code_before_md5 = $request_id . $returnUrl . $backUrl . $cp . $service . $packagename . $request_datetime . $channel . $secure_pass;
        $secure_code = md5($secure_code_before_md5);
        $note = "";
        $url = "http://dk.vinaphone.com.vn/reg.jsp?requestid=$request_id&returnurl=$returnUrl_encode&backurl=$backUrl_encode&cp=$cp&service=$service&package=$packagename&requestdatetime=$request_datetime&channel=$channel&securecode=$secure_code&language=vi&note=$note";
        return $this->redirect($url);
    }

    public function Cancel($transaction_id = '', $returnUrl = 'http://vhocde.vn/', $package = 'NGAY', $channel = 'WAP') {
        $transaction_id = self::makeRequestID();
        $request_id = $transaction_id;
        $returnUrl_encode = urlencode($returnUrl);
        $backUrl = $returnUrl;
        $backUrl_encode = urlencode($backUrl);
        $cp = "CP_DIGISOFT";
        $service = "MSTYLE";
        $packagename = $package;
        $request_datetime = date('YmdHis');
        $secure_pass = 'DIGISOFT@022016';
        $secure_code_before_md5 = $request_id . $returnUrl . $backUrl . $cp . $service . $packagename . $request_datetime . $channel . $secure_pass;
        $secure_code = md5($secure_code_before_md5);
        $note = "";
        $url = "http://dk.vinaphone.com.vn/unreg.jsp?requestid=$request_id&returnurl=$returnUrl_encode&backurl=$backUrl_encode&cp=$cp&service=$service&package=$packagename&requestdatetime=$request_datetime&channel=$channel&securecode=$secure_code&language=vi&note=$note";
        return $this->redirect($url);
    }

    public function actionBackreturn() {
        echo 'xu ly link VINA return ve';
        die;
    }

    public static function makeRequestID() {
        $id = Date('ymdHis');
        $id = '090' . $id;
        //sua lai thanh tu dong tang 000-999 dung APC
//        $idx = self::thousand_padding(round(microtime() * 1000));
        $idx = round(microtime());
        return $id . $idx;
    }

    static public function thousand_padding($number) {
        if ($number < 10) {
            return "00" . $number;
        } else if ($number < 100) {
            return "0" . $number;
        } else {
            return $number;
        }
    }

    public function actionRegisterservice() {
        if (isset($_GET['package'])) {
            $package_name = $_GET['package'];
            $package = new \app\models\Package();
            $package = $package->find()->where(['package_name' => $package_name])->one();
            if ($package == null) {
                Yii::app()->user->setFlash('thongbao', "Không tồn tại package!");
                return $this->redirect(Yii::app()->homeurl . '/site');
            }
            $this->Register($transaction_id = '', $returnUrl = 'http://vhocde.vn', $package = $package['package_name'], $channel = 'WAP');
        } else {
            Yii::app()->user->setFlash('thongbao', "Không tồn tại package!");
            return $this->redirect(Yii::app()->homeurl . '/site');
        }
//        return $this->redirect(Yii::$app->getUrlManager()->getBaseUrl() . '/');
    }

    public function actionCancelservice() {
        if (isset($_GET['package'])) {
            $package_name = $_GET['package'];
            $package = new Service();
            $package = $package->find()->where(['package_name' => $package_name])->one();
            if ($package == null) {
                Yii::app()->user->setFlash('thongbao', "Không tồn tại package!");
                return $this->redirect(Yii::app()->homeurl . '/site');
            }
            $this->Cancel($transaction_id = '', $returnUrl = 'http://vhocde.vn', $package = $package['package_name'], $channel = 'WAP');
        } else {
            Yii::app()->user->setFlash('thongbao', "Không tồn tại package!");
            return $this->redirect(Yii::app()->homeurl . '/site');
        }

//        return $this->redirect(Yii::$app->getUrlManager()->getBaseUrl() . '/');
    }
}

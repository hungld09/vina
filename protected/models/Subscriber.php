<?php

/**
 * This is the model class for table "subscriber".
 *
 * The followings are the available columns in table 'subscriber':
 * @property integer $id
 * @property string $subscriber_number
 * @property string $user_name
 * @property integer $status
 * @property string $status_log
 * @property string $email
 * @property string $full_name
 * @property string $password
 * @property string $last_login_time
 * @property integer $last_login_session
 * @property string $birthday
 * @property integer $sex
 * @property string $avatar_url
 * @property string $yahoo_id
 * @property string $skype_id
 * @property string $google_id
 * @property string $zing_id
 * @property string $facebook_id
 * @property string $create_date
 * @property string $modify_date
 * @property string $auto_recurring
 * @property integer $client_app_type
 * @property integer $using_promotion
 * @property integer $reserve_column
 *
 * The followings are the available model relations:
 * @property DownloadToken[] $downloadTokens
 * @property MsisdnIp[] $msisdnIps
 * @property ServiceSubscriberMapping[] $serviceSubscriberMappings
 * @property SmsMessage[] $smsMessages
 * @property SubscriberActivityLog[] $subscriberActivityLogs
 * @property SubscriberFeedback[] $subscriberFeedbacks
 * @property SubscriberGroupMapping[] $subscriberGroupMappings
 * @property SubscriberNotificationFilter[] $subscriberNotificationFilters
 * @property SubscriberNotificationInbox[] $subscriberNotificationInboxes
 * @property SubscriberSession[] $subscriberSessions
 * @property SubscriberTransaction[] $subscriberTransactions
 * @property SubscriberUserActionLog[] $subscriberUserActionLogs
 * @property ViewToken[] $viewTokens
 * @property VodComment[] $vodComments
 * @property VodLikeDislike[] $vodLikeDislikes
 * @property VodRating[] $vodRatings
 * @property VodSearchHistory[] $vodSearchHistories
 * @property VodSubscriberFavorite[] $vodSubscriberFavorites
 * @property VodSubscriberMapping[] $vodSubscriberMappings
 */
class Subscriber extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Subscriber the static model class
	 */
	const WIFI_PASSWORD_VALID = 0;
	const WIFI_PASSWORD_INVALID = 1;
	const WIFI_PASSWORD_EXPIRED = 2;
	const STATUS_WHITE_LIST = 10; //thue bao co status nay thi ko bi tru tien trong tat ca giao dich
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'subscriber';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_name', 'required'),
			array('status, last_login_session, sex, client_app_type, using_promotion, reserve_column', 'numerical', 'integerOnly'=>true),
			array('subscriber_number', 'length', 'max'=>45),
			array('user_name, email', 'length', 'max'=>100),
			array('full_name, password', 'length', 'max'=>200),
			array('avatar_url, yahoo_id, skype_id, google_id, zing_id, facebook_id', 'length', 'max'=>255),
			array('status_log, last_login_time, birthday, create_date, modify_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, subscriber_number, user_name, status, auto_recurring, status_log, email, full_name, password, last_login_time, last_login_session, birthday, sex, avatar_url, yahoo_id, skype_id, google_id, zing_id, facebook_id, create_date, modify_date, client_app_type, using_promotion, reserve_column', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'downloadTokens' => array(self::HAS_MANY, 'DownloadToken', 'subscriber_id'),
			'msisdnIps' => array(self::HAS_MANY, 'MsisdnIp', 'subscriber_id'),
			'serviceSubscriberMappings' => array(self::HAS_MANY, 'ServiceSubscriberMapping', 'subscriber_id'),
			'smsMessages' => array(self::HAS_MANY, 'SmsMessage', 'subscriber_id'),
			'subscriberActivityLogs' => array(self::HAS_MANY, 'SubscriberActivityLog', 'subscriber_id'),
			'subscriberFeedbacks' => array(self::HAS_MANY, 'SubscriberFeedback', 'subscriber_id'),
			'subscriberGroupMappings' => array(self::HAS_MANY, 'SubscriberGroupMapping', 'subscriber_id'),
			'subscriberNotificationFilters' => array(self::HAS_MANY, 'SubscriberNotificationFilter', 'subscriber_id'),
			'subscriberNotificationInboxes' => array(self::HAS_MANY, 'SubscriberNotificationInbox', 'subscriber_id'),
			'subscriberSessions' => array(self::HAS_MANY, 'SubscriberSession', 'subscriber_id'),
			'subscriberTransactions' => array(self::HAS_MANY, 'SubscriberTransaction', 'subscriber_id'),
			'subscriberUserActionLogs' => array(self::HAS_MANY, 'SubscriberUserActionLog', 'subscriber_id'),
			'viewTokens' => array(self::HAS_MANY, 'ViewToken', 'subscriber_id'),
			'vodComments' => array(self::HAS_MANY, 'VodComment', 'subscriber_id'),
			'vodLikeDislikes' => array(self::HAS_MANY, 'VodLikeDislike', 'subscriber_id'),
			'vodRatings' => array(self::HAS_MANY, 'VodRating', 'subscriber_id'),
			'vodSearchHistories' => array(self::HAS_MANY, 'VodSearchHistory', 'subscriber_id'),
			'vodSubscriberFavorites' => array(self::HAS_MANY, 'VodSubscriberFavorite', 'subscriber_id'),
			'vodSubscriberMappings' => array(self::HAS_MANY, 'VodSubscriberMapping', 'subscriber_id'),
			'subscriberServices' => array(self::MANY_MANY, 'Service', 'service_subscriber_mapping(subscriber_id, service_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'subscriber_number' => 'Subscriber Number',
			'user_name' => 'User Name',
			'status' => 'Status',
			'status_log' => 'Status Log',
			'email' => 'Email',
			'full_name' => 'Full Name',
			'password' => 'Password',
			'last_login_time' => 'Last Login Time',
			'last_login_session' => 'Last Login Session',
			'birthday' => 'Birthday',
			'sex' => 'Sex',
			'avatar_url' => 'Avatar Url',
			'yahoo_id' => 'Yahoo',
			'skype_id' => 'Skype',
			'auto_recurring' => 'Auto Recurring',
			'google_id' => 'Google',
			'zing_id' => 'Zing',
			'facebook_id' => 'Facebook',
			'create_date' => 'Create Date',
			'modify_date' => 'Modify Date',
			'client_app_type' => 'Client App Type',
			'using_promotion' => 'Using Promotion',
			'reserve_column' => 'Reserve Column',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('subscriber_number',$this->subscriber_number,true);
		$criteria->compare('user_name',$this->user_name,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('status_log',$this->status_log,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('full_name',$this->full_name,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('last_login_time',$this->last_login_time,true);
		$criteria->compare('last_login_session',$this->last_login_session);
		$criteria->compare('birthday',$this->birthday,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('avatar_url',$this->avatar_url,true);
		$criteria->compare('auto_recurring',$this->auto_recurring,true);
		$criteria->compare('yahoo_id',$this->yahoo_id,true);
		$criteria->compare('skype_id',$this->skype_id,true);
		$criteria->compare('google_id',$this->google_id,true);
		$criteria->compare('zing_id',$this->zing_id,true);
		$criteria->compare('facebook_id',$this->facebook_id,true);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('modify_date',$this->modify_date,true);
		$criteria->compare('client_app_type',$this->client_app_type);
		$criteria->compare('using_promotion',$this->using_promotion);
		$criteria->compare('reserve_column',$this->reserve_column);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getSubscriberTransactionsHistory($from_date, $to_date, $page, $pageSize) {
		$transactions = Yii::app()->db->createCommand()
		->select("*")
		->from("subscriber_transaction")
		->where("status=1 AND cost != 100 AND subscriber_id=:subs_id AND create_date>=:from AND create_date<=:to", array(
				':subs_id' => $this->id,
				':from'    => $from_date,
				':to'      => $to_date))
				->limit($pageSize, $page * $pageSize)
				->order("create_date DESC")
				->queryAll();
	
		$total = Yii::app()->db->createCommand()
		->select("count(*)")
		->from("subscriber_transaction")
		->where("status=1 AND cost != 100 AND subscriber_id=:subs_id AND create_date>=:from AND create_date<=:to", array(
				':subs_id' => $this->id,
				':from'    => $from_date,
				':to'      => $to_date))
				->queryScalar();
	
		//error_log("got " . count($transactions) . " this page, $total records");
	
		return array(
				'total_result'  => $total,
				'page_number'   => $page,
				'page_size'     => $pageSize,
				'subscriber_id' => $this->id,
				'total_page'    => intval($total / $pageSize) + 1,
				'data'          => $transactions
		);
	}
	
	public static function newSubscriber($msisdn) {
		$subscriber = Subscriber::model()->findByAttributes(array("subscriber_number" => $msisdn));
		if($subscriber != NULL) {
			return $subscriber;
		}
		
		$aDate = new DateTime();
		$subscriber = new Subscriber();
	
		$subscriber->subscriber_number = $msisdn;
		$subscriber->user_name = $msisdn;
		$subscriber->status = 1;
		$subscriber->create_date = $aDate->format('Y-m-d H:i:s');
		$subscriber->modify_date = $aDate->format('Y-m-d H:i:s');
	
		if($subscriber->save()) {
			return $subscriber;
		}
		return null;
	}
	
	public function newTransaction($channel_type, $using_type, $purchase_type, $service = null, $asset = null, $time = null, $user_name = null, $user_ip = null) {
		Yii::log("newTransaction channel_type = ".$channel_type." id = ".$this->id);
		if($time == null){
			$aDate = new DateTime();
		}else{
			$aDate = new DateTime($time);
		}
		$subscriberTransaction = new SubscriberTransaction();
	
		if ($service != null) {
			$subscriberTransaction->service_id = $service->id;
		}
		if($user_name != null) {
			$subscriberTransaction->vnp_username = $user_name;
		}
		if($user_ip != null) {
			$subscriberTransaction->vnp_ip = $user_ip;
		}
	
		$subscriberTransaction->subscriber_id = $this->id;
		$subscriberTransaction->create_date = $aDate->format('Y-m-d H:i:s');
		$subscriberTransaction->status = 2; //dat la fail, check status=CPS_OK thi update ve 1
		if ($using_type == 1) { // dang ky dich vu
			if ($purchase_type == 2) { // gia han
				$subscriberTransaction->description = isset($service) ? $service->display_name : "";
			} else {
				$subscriberTransaction->description = isset($service) ? $service->display_name : "";
			}
			$subscriberTransaction->cost = isset($service) ? $service->price : 0;
		} else if ($using_type == 2) { // mua le
			$subscriberTransaction->description = "HOCDE" . (isset($asset) ? " " . $asset->display_name . " [".$asset->id."]" : "");
			$subscriberTransaction->cost = isset($asset) ? $asset->price : 0;
		}  else if ($using_type == USING_TYPE_CHARGING_SMS){
			$subscriberTransaction->cost = 100;
			$subscriberTransaction->description = "sms";
		} else {
			$subscriberTransaction->cost = 0;
		}
		$subscriberTransaction->error_code = "UNKNOWN";
		if($this->status == self::STATUS_WHITE_LIST) {
			$subscriberTransaction->cost = 0;
			$subscriberTransaction->error_code = "free";
		}
		$subscriberTransaction->channel_type = $channel_type; // WEB, WAP, SMS, APP, ...
	
		//TODO:event_id dung lam gi?
		//$subscriberTransaction->event_id = '';
		$subscriberTransaction->using_type = $using_type;//0: mua dich vu, 1: mua cau hoi,
		$subscriberTransaction->purchase_type = $purchase_type; //0: mua moi, 1: gia han, 2: chu dong huy, 3: bi huy
//		$subscriberTransaction->save();
//                echo '<pre>'; print_r($subscriberTransaction);die;
                if(!$subscriberTransaction->save()){
                    echo '<pre>'; print_r($subscriberTransaction->getErrors());
                }
//                echo 1;die;
		return $subscriberTransaction;
	}
	
	public function newExtendTimeTransaction($subscriber, $channel_type, $duration, $price, $charging_code) {
		$subscriberTransaction = new SubscriberTransaction;
		$subscriberTransaction->using_type = USING_TYPE_EXTEND_TIME;
		$subscriberTransaction->purchase_type = SubscriberTransaction::PURCHASE_TYPE_EXTEND_TIME;
		$subscriberTransaction->cost = $price;
		$subscriberTransaction->error_code = $charging_code;
		if($charging_code == "0") {
			$subscriberTransaction->status = 1;
		}
		else {
			$subscriberTransaction->status = 2;
		}
		$subscriberTransaction->create_date = new CDbExpression("NOW()");
		$subscriberTransaction->subscriber_id = $subscriber->id;
		$subscriberTransaction->description = "Gia han $duration ph";
		$subscriberTransaction->save();
	
		return $subscriberTransaction;
	}
	
	public function newOrder($channel_type, $using_type, $purchase_type, $service = null, $asset = null, $status = 2, $productOrderKey = null) {
		$aDate = new DateTime();
		$subscriberOrder = new SubscriberOrder();
	
		if ($service != null) {
			$subscriberOrder->service_id = $service->id;
		}
		if ($asset != null) {
			$subscriberOrder->vod_asset_id = $asset->id;
		}
	
		$subscriberOrder->subscriber_id = $this->id;
		$subscriberOrder->create_date = $aDate->format("Y-m-d H:i:s");
		$subscriberOrder->status = $status; // status dung lam gi khi co error_code?
	
		if ($using_type == 1) { // dang ky dich vu
			if ($purchase_type == 2) { // gia han
				$subscriberOrder->description = isset($service) ? "Gia han ".$service->display_name : "";
			} else {
				$subscriberOrder->description = isset($service) ? "Dang ky ".$service->display_name : "";
			}
			$subscriberOrder->cost = isset($service) ? $service->price : 0;
		} else if ($using_type == 2) { // mua phim
			$subscriberOrder->description = "Phim" . (isset($asset) ? " " . $asset->display_name . " [".$asset->id."]" : "");
			$subscriberOrder->cost = isset($asset) ? $asset->price : 0;
		} else if ($using_type == 3) { // tai phim
			$subscriberOrder->description = "Phim" . (isset($asset) ? " " . $asset->display_name . " [".$asset->id."]" : "");
			$subscriberOrder->cost = isset($asset) ? $asset->price_download : 0;
		} else if ($using_type == 4) { // tang phim
			$subscriberOrder->description = "Phim" . (isset($asset) ? " " . $asset->display_name . " [".$asset->id."]" : "");
			$subscriberOrder->cost = isset($asset) ? $asset->price_gift : 0;
		} else if ($using_type == 4) { // duoc tang phim
			$subscriberOrder->description = "Phim" . (isset($asset) ? " " . $asset->display_name . " [".$asset->id."]" : "");
			$subscriberOrder->cost = 0;
		} else {
			$subscriberOrder->cost = 0; //vi du using_type == 0 -> huy dich vu
                        $subscriberOrder->description = isset($service) ? "Huy ".$service->display_name : "";
		}
	
		$subscriberOrder->channel_type = $channel_type; // WEB, WAP, SMS, APP, ...
	
		//TODO:event_id dung lam gi?
		//$subscriberOrder->event_id = '';
		$subscriberOrder->using_type = $using_type;//0: mua dich vu, 1: mua phim, 2: tai phim, 3: tang phim
		$subscriberOrder->purchase_type = $purchase_type; //0: mua moi, 1: gia han, 2: chu dong huy, 3: bi huy
		if($status != 1) {
                    $subscriberOrder->error_code = "ERROR";
                }
                else {
                    $subscriberOrder->error_code = "SUCCESS";
                }
		$subscriberOrder->product_order_key = $productOrderKey;
		$subscriberOrder->save();
	
		return $subscriberOrder;
	}
	
	public function newRequest($channel_type, $using_type, $purchase_type, $service = null, $asset = null, $status = 2) {
		$aDate = new DateTime();
		$subscriberRequest = new SubscriberRequest();
	
		if ($service != null) {
			$subscriberRequest->service_id = $service->id;
		}
		if ($asset != null) {
			$subscriberRequest->vod_asset_id = $asset->id;
		}
	
		$subscriberRequest->subscriber_id = $this->id;
		$subscriberRequest->create_date = $aDate->format("Y-m-d H:i:s");
		$subscriberRequest->status = $status; // status dung lam gi khi co error_code?
	
		if ($using_type == 1) { // dang ky dich vu
			if ($purchase_type == 2) { // gia han
				$subscriberRequest->description = isset($service) ? $service->display_name : "";
			} else {
				$subscriberRequest->description = isset($service) ? $service->display_name : "";
			}
			$subscriberRequest->cost = isset($service) ? $service->price : 0;
		} else if ($using_type == 2) { // mua phim
			$subscriberRequest->description = "Phim" . (isset($asset) ? " " . $asset->display_name . " [".$asset->id."]" : "");
			$subscriberRequest->cost = isset($asset) ? $asset->price : 0;
		} else if ($using_type == 3) { // tai phim
			$subscriberRequest->description = "Phim" . (isset($asset) ? " " . $asset->display_name . " [".$asset->id."]" : "");
			$subscriberRequest->cost = isset($asset) ? $asset->price_download : 0;
		} else if ($using_type == 4) { // tang phim
			$subscriberRequest->description = "Phim" . (isset($asset) ? " " . $asset->display_name . " [".$asset->id."]" : "");
			$subscriberRequest->cost = isset($asset) ? $asset->price_gift : 0;
		} else if ($using_type == 4) { // duoc tang phim
			$subscriberRequest->description = "Phim" . (isset($asset) ? " " . $asset->display_name . " [".$asset->id."]" : "");
			$subscriberRequest->cost = 0;
		} else {
			$subscriberRequest->cost = 0; //vi du using_type == 0 -> huy dich vu
		}
	
		$subscriberRequest->channel_type = $channel_type; // WEB, WAP, SMS, APP, ...
	
		//TODO:event_id dung lam gi?
		//$subscriberRequest->event_id = '';
		$subscriberRequest->using_type = $using_type;//0: mua dich vu, 1: mua phim, 2: tai phim, 3: tang phim
		$subscriberRequest->purchase_type = $purchase_type; //0: mua moi, 1: gia han, 2: chu dong huy, 3: bi huy
		$subscriberRequest->error_code = "UNKNOWN";
		$subscriberRequest->save();
	
		return $subscriberRequest;
	}
	
	public function addService($service,$partner_id = NULL, $using_type = USING_TYPE_REGISTER, $time = null) {
		if($this->status == 2) { //thue bao o trang thai huy thi sua lai thanh active
			$this->status = 1;
			$this->update();
		}
		
		if($time == null){
			$aDate = new DateTime();
			$eDate = new DateTime();
		}else{
			$aDate = new DateTime($time);
			$eDate = new DateTime($time);
		}
		if (isset($service->using_days) && $service->using_days > 0) {
			$eDate->add(DateInterval::createFromDateString(($service->using_days - 1) . ' days'));//hiendv: chi duoc su dung dv den het ngay (using_days - 1) (d-m-Y 23:59:59) cua chu ky
		}
		
		$extendPendingSsm = ServiceSubscriberMapping::model()->findByAttributes(array("subscriber_id" => $this->id, "is_active" => ServiceSubscriberMapping::SERVICE_STATUS_EXTEND_PENDING));
		if($extendPendingSsm != NULL) {
			$extendPendingSsm->is_active = ServiceSubscriberMapping::SERVICE_STATUS_INACTIVE;
			$extendPendingSsm->modify_date = new CDbExpression("NOW()");
			$extendPendingSsm->update();
		}
		
		$subscriberService = new ServiceSubscriberMapping;
		$subscriberService->service_id = $service->id;
		$subscriberService->subscriber_id = $this->id;
		$subscriberService->activate_date = $aDate->format("Y-m-d H:i:s");
		$subscriberService->expiry_date = $eDate->format("Y-m-d 23:59:59");
		$subscriberService->is_active = ServiceSubscriberMapping::SERVICE_STATUS_ACTIVE;
		$subscriberService->create_date = $aDate->format("Y-m-d H:i:s");
		$subscriberService->is_deleted = 0;
		$subscriberService->view_count = 0;
		$subscriberService->partner_id = $partner_id;
		$subscriberService->sent_notification = 0;
		$subscriberService->save();
	
		return $subscriberService;
	}
	
	public function cancelService($smap, $channel_type, $requestid = -1, $description="", $username=null, $userip=null) {
		$transaction = $this->newTransaction($channel_type, USING_TYPE_CANCEL, SubscriberTransaction::PURCHASE_TYPE_CANCEL, $smap->service);
		$chargingResult = 'UNKNOWN';
		$chargingResult = ChargingProxy::chargingCancel($username, $userip, $this->subscriber_number, $smap->service, $transaction->id, $channel_type);
		if($chargingResult == CPS_OK) {
			$smap->is_active = ServiceSubscriberMapping::SERVICE_STATUS_INACTIVE;
			$smap->is_deleted = 1;
			$smap->modify_date = new CDbExpression('NOW()');
			$smap->update();
			$transaction->error_code = $chargingResult;
			$transaction->status = 1;
		}
		else {
			$transaction->status = 2;
			$transaction->error_code = $chargingResult;
		}
		if($requestid != -1) {
			$transaction->req_id = $requestid;
			$transaction->description = $description;
			$transaction->vnp_username = $username;
			$transaction->vnp_ip = $userip;
		}
		else if($username != null) {
			$transaction->vnp_username = $username;
			$transaction->vnp_ip = $userip;
		}
		$transaction->update();
		return $chargingResult;
	}

    public function cancelService2($smap, $channel_type, $requestid = -1, $description="", $username=null, $userip=null) {
        if($channel_type != 'CSKH'){
            $transaction = $this->newTransaction($channel_type, USING_TYPE_CANCEL, SubscriberTransaction::PURCHASE_TYPE_CANCEL, $smap->service);
            $chargingResult = 'UNKNOWN';
            $chargingResult = ChargingProxy_test::chargingCancel($username, $userip, $this->subscriber_number, $smap->service, $transaction->id, $channel_type);
            if(($chargingResult == CPS_OK) || ($chargingResult == '6')) {
                $smap->is_active = ServiceSubscriberMapping::SERVICE_STATUS_INACTIVE;
                $smap->is_deleted = 1;
                $smap->modify_date = new CDbExpression('NOW()');
                $smap->update();
                $transaction->error_code = $chargingResult;
                $transaction->status = 1;
            }
            else {
                $transaction->status = 2;
                $transaction->error_code = $chargingResult;
            }
            if($requestid != -1) {
                $transaction->req_id = $requestid;
                $transaction->description = $description;
                $transaction->vnp_username = $username;
                $transaction->vnp_ip = $userip;
            }
            else if($username != null) {
                $transaction->vnp_username = $username;
                $transaction->vnp_ip = $userip;
            }
            $transaction->update();
            return $chargingResult;
        }else{
            $chargingResult = 'UNKNOWN';
            $chargingResult = ChargingProxy_test::chargingCancel($username, $userip, $this->subscriber_number, $smap->service, time(), $channel_type);
            if(($chargingResult == CPS_OK) || ($chargingResult == '6')) {
                $smap->is_active = ServiceSubscriberMapping::SERVICE_STATUS_INACTIVE;
                $smap->is_deleted = 1;
                $smap->modify_date = new CDbExpression('NOW()');
                $smap->update();
            }
            return $chargingResult;
        }
    }
	public function pendingService($smap, $channel_type, $time = null) {
		if($time == null){
			$aDate = new DateTime();
		}else{
			$aDate = new DateTime($time);
		}
		$transaction = new SubscriberTransaction();
		$transaction->service_id = $smap->service->id;
		$transaction->subscriber_id = $this->id;
		$transaction->create_date = $aDate->format("Y-m-d H:i:s");
		$transaction->status = 1; // status dung lam gi khi co error_code?
		$transaction->description = $smap->service->display_name;
		$transaction->cost = 0;
		$transaction->channel_type = $channel_type;
		//TODO:event_id dung lam gi?
		//$transaction->event_id = '';
		$transaction->using_type = 0; //cu cho bang zero, chi check purchase_type
		$transaction->purchase_type = SubscriberTransaction::PURCHASE_TYPE_PENDING; //pending dich vu
		$transaction->error_code = CPS_OK;
		$transaction->save();
		$smap->is_active = ServiceSubscriberMapping::SERVICE_STATUS_PENDING;
        $smap->modify_date = new CDbExpression('NOW()');
		$smap->update();
	}
	
	public function restoreService($smap, $channel_type, $time = null) {
		if($time == null){
			$aDate = new DateTime();
		}else{
			$aDate = new DateTime($time);
		}
		$transaction = new SubscriberTransaction();
		$transaction->service_id = $smap->service->id;
		$transaction->subscriber_id = $this->id;
		$transaction->create_date = $aDate->format("Y-m-d H:i:s");
		$transaction->status = 1; // status dung lam gi khi co error_code?
		$transaction->description = $smap->service->display_name;
		$transaction->cost = 0;
		$transaction->channel_type = $channel_type;
		//TODO:event_id dung lam gi?
		//$transaction->event_id = '';
		$transaction->using_type = 0; //cu cho bang zero, chi check purchase_type
		$transaction->purchase_type = SubscriberTransaction::PURCHASE_TYPE_RESTORE; //pending dich vu
		$transaction->error_code = CPS_OK;
		$transaction->save();
		$smap->is_active = ServiceSubscriberMapping::SERVICE_STATUS_ACTIVE;
		$smap->sent_notification = 0;
        $smap->modify_date = new CDbExpression('NOW()');
		$smap->update();
	}
	
	public function addVodAsset($vod, $using_type, $debit = true) {
		$aDate = new DateTime();

		//Add 1 ngay cho film mua
		$eDate = new DateTime();
		$eDate->add(DateInterval::createFromDateString('1 days'));

		$vs = new VodSubscriberMapping();
		
		$vs->vod_asset_id = $vod->id;
		$vs->subscriber_id = $this->id;
		$vs->description = "";
		$vs->activate_date = $aDate->format("Y-m-d H:i:s");
		$vs->expiry_date = $eDate->format("Y-m-d H:i:s");
		$vs->is_active = 1;
		$vs->create_date = $aDate->format("Y-m-d H:i:s");
		$vs->is_deleted = 0;
		$vs->using_type = $using_type;
		$vs->save();
		return $vs;
	}
	
	public function hasVodAsset($vod, $using_type) {
		$vs = VodSubscriberMapping::model()->findByAttributes(array(
				"subscriber_id" => $this->id,
				"vod_asset_id"  => $vod->id,
				"using_type"    => $using_type,
				"is_active"     => 1,
				"is_deleted"    => 0));

		//Check expiry date cua film
		if(isset($vs)){
			$current = time();
			if(strtotime($vs->expiry_date) < $current){
				$vs->is_active = 0;
				$vs->update();
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return FALSE;
		}
		
		return FALSE;
		//return isset($vs) ? $vs : FALSE;
	}
	
	public static function getAutoRecurringSubscribers() {
		$timestamp = time() + 6 * 60 * 60; // next 1/2 days
		$subscribers = Yii::app()->db->createCommand()
			->select("ssm.id, ssm.subscriber_id, ssm.service_id, ssm.expiry_date, UNIX_TIMESTAMP(ssm.expiry_date) AS expiry_ts, ssm.sent_notification, ssm.recur_retry_times, sub.subscriber_number , sub.auto_recurring, s.code_name, s.display_name, s.price, s.using_days")
			->from("subscriber sub")
			->join("service_subscriber_mapping ssm", "sub.id=ssm.subscriber_id")
			->join("service s", "ssm.service_id=s.id")
			->where("sub.auto_recurring=1 AND ssm.is_active=1 AND ssm.is_deleted=0 AND ((ssm.expiry_date BETWEEN NOW() AND FROM_UNIXTIME(:timestamp)) OR ssm.recur_retry_times > 0)", array(':timestamp' => $timestamp))
			->queryAll();
		return $subscribers;
	}
	
	public function purchaseVideo($asset_id) {
		$asset = VodAsset::model()->findByPk($asset_id);
		if ($asset) {
				
		} else {
				
		}
	}
	
	public function purchaseVideoEpisode($episode_id) {
	
	}
	
	public function purchaseDownload($asset_id) {
	
	}
	
	public function purchaseGift($asset_id, $dst_subscriber) {
	
	}
	
	public function inactiveOtherServices() {
		$arrSsm = ServiceSubscriberMapping::model()->findAllByAttributes(array('subscriber_id' => $this->id, 'is_active' => 1));
		foreach($arrSsm as $ssm) {
			$ssm->is_active = 0;
			$ssm->update();
		}
	}
	
	public function isUsingService() {
		$ssm = ServiceSubscriberMapping::model()->findByAttributes(array('subscriber_id' => $this->id, 'is_active' => 1));
		if($ssm != NULL) {
			return true;
		}
		return false;
	}

	public function getUsingService() {
		$ssm = ServiceSubscriberMapping::model()->findByAttributes(array('subscriber_id' => $this->id, 'is_active' => 1));
		return $ssm;
	}

	public function isFirstUse() {
		$ssm = ServiceSubscriberMapping::model()->findByAttributes(array('subscriber_id' => $this->id));
		if($ssm == NULL) {
			return true;
		}
		return false;
	}
	
	public function getFreeWatchingTime() {
		$ssm = ServiceSubscriberMapping::model()->findByAttributes(array('subscriber_id' => $this->id, 'is_active' => 1));
		if($ssm == NULL) {
			return 0;
		}
		return $ssm->watching_time;
	}
	
	public static function getAutoRecurringSubscribersByThread($thread_pool = 1, $thread_index = 0) {
//		$timestamp = time() + 3 * 60 * 60; // next 3 tieng
		$timestamp = time(); // sua theo y/c
		$firstTime = time() - 24*60*60; // Lui lai 1 ngay
		//hiendv test 
		//$firstTime = time() - 5*60; // Lui lai 1 ngay
		$lastModifyDate = date('Y-m-d H:i:s', time()-21*3600);
		
		$subscribers = Yii::app()->db->createCommand()
		// 			->select("ssm.id, ssm.subscriber_id, ssm.service_id, ssm.expiry_date, UNIX_TIMESTAMP(ssm.expiry_date) AS expiry_ts, ssm.sent_notification, ssm.recur_retry_times, sub.subscriber_number , sub.auto_recurring, s.code_name, s.display_name, s.price, s.using_days")
		->select("ssm.id, ssm.subscriber_id, ssm.service_id, ssm.expiry_date, ssm.modify_date, UNIX_TIMESTAMP(ssm.expiry_date) AS expiry_ts, ssm.sent_notification, ssm.recur_retry_times, sub.subscriber_number , sub.auto_recurring")
		->from("subscriber sub")
		->join("service_subscriber_mapping ssm", "sub.id=ssm.subscriber_id")
		// 			->join("service s", "ssm.service_id=s.id")
		->where("(ssm.id % $thread_pool = $thread_index) AND sub.auto_recurring=1 AND ssm.is_active=1 AND ((ssm.expiry_date BETWEEN FROM_UNIXTIME(:firsttime) AND FROM_UNIXTIME(:timestamp)) or (ssm.recur_retry_times > 0 AND (ssm.modify_date < '$lastModifyDate' OR ssm.modify_date is null)))", array(':timestamp' => $timestamp,':firsttime'=>$firstTime))
		->order("ssm.recur_retry_times")
		->queryAll();
		return $subscribers;
	}
	
	public function generateWifiPassword($sendSMS = true) {
// 		$password = CUtils::randomString(4, "1234567890");
// 		$this->password = $password;
// 		$expiryTime = date("d/m/Y H:i:s", time()+86400);
// 		$this->last_login_time = date("Y:m:d H:i:s", time()+86400);
// 		$content = "VinaPhone gui Quy Khach mat khau dang nhap wifi $password . Mat khau co hieu luc trong 24 gio.";
// 		$this->update();
// 		if(!$sendSMS){
// 			return $content;
// 		}
// 		$mt = Vinaphone::sendSms($this->subscriber_number, $content);
// 		if($mt->mt_status != 0) {
// 			return -1;
// 		}
		
// 		return 1;

		$apiService = new APIService();
		$response = $apiService->getOTP($this->subscriber_number);
		return $response;
	}
	
	public function loginByWifiPassword($password) {
		$status = Subscriber::WIFI_PASSWORD_VALID;
		$session_id = -1;
		$apiService = new APIService();
		Yii::log("Login wifi $this->subscriber_number pw $password");
		$response = $apiService->checkOTP($this->subscriber_number, $password);
		if($response == 0) { //dang nhap thanh cong bang otp
// 			if(strtotime($this->last_login_time) > time()) {
				$session = SubscriberSession::createNewSession($this->id);
				$status = Subscriber::WIFI_PASSWORD_VALID;
				$session_id = $session->session_id; 
// 			}
// 			else {
// 				$status = Subscriber::WIFI_PASSWORD_EXPIRED; 
// 			}
		}
		else {
			$status = Subscriber::WIFI_PASSWORD_INVALID;
		}
		Yii::log("Login wifi result: "+$status);
		$result = array('status' => $status, 'session_id' => $session_id);
		return $result;
	}
	
	public function getErrorMessage($error_code) {
		switch($error_code) {
			case Subscriber::WIFI_PASSWORD_EXPIRED:
				return "Mật khẩu đã hết hạn sử dụng";
			case Subscriber::WIFI_PASSWORD_INVALID:
				return "Mật khẩu không đúng";
			case Subscriber::WIFI_PASSWORD_VALID:
				return "Đăng nhập thành công";
		}
	}
	
	public function getFakeSubscriber() {
		$subscriber = Subscriber::model()->findByPk(1);
		if($subscriber == NULL) {
			$subscriber = self::newSubscriber('84986636879');
		}
		return $subscriber;
	}
	public function isNeverUsed() {
		$arrSsm = ServiceSubscriberMapping::model()->findAllByAttributes(array('subscriber_id' => $this->id));
		if(count($arrSsm) == 0) {
			return true;
		}
		return false;
	} 
        public function newTransactionServiceQuestion($purchase_type, $price, $subscriber) {
            $aDate = new DateTime();
//            $partner = (strtolower($subscriber->partner_id) != 'hs1' || strtolower($subscriber->partner_id) != 'hs' || $subscriber->partner_id == null || $subscriber->partner_id == ''|| $subscriber->partner_id != 'test') ? 'net2e' : strtolower($subscriber->partner_id);
            $subscriberTransactionService = new SubscriberTransactionService();
            $subscriberTransactionService->status = 2;
            $subscriberTransactionService->subscriber_id = $subscriber->id;
            $subscriberTransactionService->service_id = 0;
            $subscriberTransactionService->description = "UNKNOWN";
            $subscriberTransactionService->cost = $price;
            $subscriberTransactionService->create_date = $aDate->format("Y-m-d H:i:s");
            $subscriberTransactionService->purchase_type = $purchase_type;
            if(!$subscriberTransactionService->save()) {
                     var_dump($subscriberTransactionService->getErrors());die;
//            Yii::log("Save subscriber transaction fail:".$subscriberTransaction->getErrors());
            }
            return $subscriberTransactionService;
	}
}
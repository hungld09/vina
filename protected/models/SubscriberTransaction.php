<?php

/**
 * This is the model class for table "subscriber_transaction".
 *
 * The followings are the available columns in table 'subscriber_transaction':
 * @property integer $id
 * @property string $create_date
 * @property string $issuer
 * @property string $card_seria
 * @property string $card_code
 * @property string $error_code
 * @property integer $status
 * @property string $description
 * @property integer $subscriber_id
 * @property integer $purchase_type
 */
class SubscriberTransaction extends CActiveRecord
{
        const PURCHASE_TYPE_NEW = 1;
	const PURCHASE_TYPE_EXTEND = 2;
	const PURCHASE_TYPE_CANCEL = 3;
	const PURCHASE_TYPE_FORCE_CANCEL = 4;
	const PURCHASE_TYPE_PENDING = 5;
	const PURCHASE_TYPE_RESTORE = 6;
	const PURCHASE_TYPE_EXTEND_TIME = 7;
	const PURCHASE_TYPE_RETRY_EXTEND = 10;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'subscriber_transaction';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_date', 'required'),
			array('status, subscriber_id, purchase_type', 'numerical', 'integerOnly'=>true),
			array('issuer, card_seria, card_code, error_code', 'length', 'max'=>45),
			array('description', 'length', 'max'=>200),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, create_date, issuer, card_seria, card_code, error_code, partner_id, oncash, status, description, subscriber_id, purchase_type', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'create_date' => 'Create Date',
			'issuer' => 'Issuer',
			'card_seria' => 'Card Seria',
			'card_code' => 'Card Code',
			'error_code' => 'Error Code',
			'status' => 'Status',
			'description' => 'Description',
			'subscriber_id' => 'Subscriber',
			'partner_id' => 'Partner Id',
			'purchase_type' => '1 - nap the
2 - mua cau hoi
',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('issuer',$this->issuer,true);
		$criteria->compare('card_seria',$this->card_seria,true);
		$criteria->compare('card_code',$this->card_code,true);
		$criteria->compare('error_code',$this->error_code,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('subscriber_id',$this->subscriber_id);
		$criteria->compare('partner_id',$this->partner_id);
		$criteria->compare('purchase_type',$this->purchase_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SubscriberTransaction the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
	public static function getMaxTransactionId() {
		$result = Yii::app()->db->createCommand()
			->selectDistinct('t.id')
			->from('subscriber_transaction as t')
			->limit(0, 1)
			->order('t.id DESC')
			->queryScalar();
		if (!$result) {
			$result = 1;
		}
		return $result;
	}
	
	public static function getTypeName($purchae_type) {
		switch($purchae_type) {
			case SubscriberTransaction::PURCHASE_TYPE_NEW:
				return "Đăng ký";
			case SubscriberTransaction::PURCHASE_TYPE_EXTEND_TIME:
				return "Gia hạn";
			case SubscriberTransaction::PURCHASE_TYPE_RETRY_EXTEND:
				return "Truy thu";
			case SubscriberTransaction::PURCHASE_TYPE_CANCEL:
				return "Hủy";
			case SubscriberTransaction::PURCHASE_TYPE_FORCE_CANCEL:
				return "Bị hủy";
			case SubscriberTransaction::PURCHASE_TYPE_PENDING:
				return "Tạm dừng";
			case SubscriberTransaction::PURCHASE_TYPE_RESTORE:
				return "Khôi phục";
		}
	}
}

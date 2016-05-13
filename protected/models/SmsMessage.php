<?php

/**
 * This is the model class for table "sms_message".
 *
 * The followings are the available columns in table 'sms_message':
 * @property integer $id
 * @property string $type
 * @property string $source
 * @property string $destination
 * @property string $message
 * @property string $received_time
 * @property string $sending_time
 * @property integer $mo_id
 * @property integer $subscriber_id
 * @property string $mt_status
 * @property string $mo_status
 *
 * The followings are the available model relations:
 * @property SmsMessage $mo
 * @property SmsMessage[] $smsMessages
 * @property Subscriber $subscriber
 */
class SmsMessage extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SmsMessage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sms_message';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('received_time', 'required'),
			array('mo_id, subscriber_id', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>2),
			array('source, destination', 'length', 'max'=>20),
			array('message', 'length', 'max'=>1000),
			array('mt_status', 'length', 'max'=>500),
			array('sending_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, source, destination, message, received_time, sending_time, mo_id, subscriber_id, mt_status, mo_status', 'safe', 'on'=>'search'),
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
			'mo' => array(self::BELONGS_TO, 'SmsMessage', 'mo_id'),
			'smsMessages' => array(self::HAS_MANY, 'SmsMessage', 'mo_id'),
			'subscriber' => array(self::BELONGS_TO, 'Subscriber', 'subscriber_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'source' => 'Source',
			'destination' => 'Destination',
			'message' => 'Message',
			'received_time' => 'Received Time',
			'sending_time' => 'Sending Time',
			'mo_id' => 'Mo',
			'subscriber_id' => 'Subscriber',
			'mt_status' => 'Mt Status',
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('source',$this->source,true);
		$criteria->compare('destination',$this->destination,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('received_time',$this->received_time,true);
		$criteria->compare('sending_time',$this->sending_time,true);
		$criteria->compare('mo_id',$this->mo_id);
		$criteria->compare('subscriber_id',$this->subscriber_id);
		$criteria->compare('mt_status',$this->mt_status,true);
		$criteria->compare('mo_status',$this->mo_status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
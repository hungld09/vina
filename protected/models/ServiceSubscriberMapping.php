<?php

/**
 * This is the model class for table "service_subscriber_mapping".
 *
 * The followings are the available columns in table 'service_subscriber_mapping':
 * @property integer $id
 * @property integer $subscriber_id
 * @property integer $service_id
 * @property integer $is_active
 * @property string $expiry_date
 * @property string $create_date
 * @property integer $question_free
 *
 * The followings are the available model relations:
 * @property Subscriber $subscriber
 * @property Service $service
 */
class ServiceSubscriberMapping extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'service_subscriber_mapping';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subscriber_id', 'required'),
			array('subscriber_id, service_id, is_active, question_free', 'numerical', 'integerOnly'=>true),
			array('expiry_date, create_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, subscriber_id, service_id, coin, is_active, expiry_date, create_date, question_free', 'safe', 'on'=>'search'),
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
			'subscriber' => array(self::BELONGS_TO, 'Subscriber', 'subscriber_id'),
			'service' => array(self::BELONGS_TO, 'Service', 'service_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'subscriber_id' => 'Subscriber',
			'service_id' => 'Service',
			'is_active' => 'Is Active',
			'expiry_date' => 'Expiry Date',
			'create_date' => 'Create Date',
			'question_free' => 'Question Free',
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
		$criteria->compare('subscriber_id',$this->subscriber_id);
		$criteria->compare('service_id',$this->service_id);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('expiry_date',$this->expiry_date,true);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('question_free',$this->question_free);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ServiceSubscriberMapping the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function isRegisteredInThreeMonth($subscriber_id){
            $date = date("Y-m-d H:i:s");
            $previewDate = strtotime($date."- 3 months");
            $previewDate = date("Y-m-d H:i:s",$previewDate);
    //        $sqlString ="select * from service_subscriber_mapping where subscriber_id = '$subscriber_id' and create_date > '$previewDate'";
            $sqlString ="select * from service_subscriber_mapping where subscriber_id = '$subscriber_id' and (create_date > '$previewDate' or (modify_date is not null and modify_date > '$previewDate' ))";
            $serviceMapping = ServiceSubscriberMapping::model()->findAllBySql($sqlString, array());
            if(count($serviceMapping) > 0) return 1;
            else return 0;
        }
}

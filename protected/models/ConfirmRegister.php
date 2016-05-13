<?php

/**
 * This is the model class for table "confirm_register".
 *
 * The followings are the available columns in table 'confirm_register':
 * @property integer $id
 * @property integer $subscriber_id
 * @property string $sessionkey
 * @property integer $end_time
 */
class ConfirmRegister extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'confirm_register';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subscriber_id, sessionkey, end_time', 'required'),
			array('subscriber_id, end_time', 'numerical', 'integerOnly'=>true),
			array('sessionkey', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, subscriber_id, sessionkey, end_time', 'safe', 'on'=>'search'),
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
			'subscriber_id' => 'Subscriber',
			'sessionkey' => 'Sessionkey',
			'end_time' => 'End Time',
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
		$criteria->compare('sessionkey',$this->sessionkey,true);
		$criteria->compare('end_time',$this->end_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ConfirmRegister the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

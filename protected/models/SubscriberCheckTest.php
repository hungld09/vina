<?php

/**
 * This is the model class for table "subscriber_check_test".
 *
 * The followings are the available columns in table 'subscriber_check_test':
 * @property integer $id
 * @property integer $subscriber_id
 * @property integer $class_id
 * @property integer $subject_id
 * @property integer $point
 * @property integer $times
 * @property string $create_date
 */
class SubscriberCheckTest extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'subscriber_check_test';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subscriber_id, class_id, subject_id', 'required'),
			array('subscriber_id, class_id, subject_id, point, times', 'numerical', 'integerOnly'=>true),
			array('create_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, subscriber_id, class_id, subject_id, point, times, create_date, start_time, end_time', 'safe', 'on'=>'search'),
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
			'class_id' => 'Class',
			'subject_id' => 'Subject',
			'point' => 'Point',
			'times' => 'Times',
			'create_date' => 'Create Date',
			'start_time' => 'Start Time',
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
		$criteria->compare('class_id',$this->class_id);
		$criteria->compare('subject_id',$this->subject_id);
		$criteria->compare('point',$this->point);
		$criteria->compare('times',$this->times);
		$criteria->compare('start_time',$this->start_time);
		$criteria->compare('end_time',$this->end_time);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SubscriberCheckTest the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

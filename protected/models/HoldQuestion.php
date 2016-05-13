<?php

/**
 * This is the model class for table "hold_question".
 *
 * The followings are the available columns in table 'hold_question':
 * @property integer $id
 * @property integer $question_id
 * @property integer $subscriber_id
 * @property integer $start_time
 * @property integer $end_time
 */
class HoldQuestion extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hold_question';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('question_id, start_time, end_time', 'required'),
			array('question_id, start_time, end_time, subscriber_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, question_id, start_time, status, subscriber_id, end_time', 'safe', 'on'=>'search'),
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
			'question_id' => 'Question',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'status' => 'Status',
			'subscriber_id' => 'Subscriber Id',
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
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('start_time',$this->start_time);
		$criteria->compare('end_time',$this->end_time);
		$criteria->compare('status',$this->status);
		$criteria->compare('subscriber_id',$this->subscriber_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HoldQuestion the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

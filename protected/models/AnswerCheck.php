<?php

/**
 * This is the model class for table "answer_check".
 *
 * The followings are the available columns in table 'answer_check':
 * @property integer $id
 * @property integer $question_id
 * @property integer $status
 * @property string $create_date
 */
class AnswerCheck extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'answer_check';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('answer_id, subscriber_id, status, create_date', 'required'),
			array('answer_id, type, subscriber_id, status', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, answer_id, type, subscriber_id, status, create_date', 'safe', 'on'=>'search'),
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
			'answer_id' => 'Answer Id',
			'status' => 'Status',
			'type' => 'Type',
			'create_date' => 'Create Date',
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
		$criteria->compare('answer_id',$this->answer_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('title',$this->title);
		$criteria->compare('subscriber_id',$this->subscriber_id,true);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AnswerCheck the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

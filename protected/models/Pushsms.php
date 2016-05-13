<?php

/**
 * This is the model class for table "pushsms".
 *
 * The followings are the available columns in table 'pushsms':
 * @property integer $id
 * @property string $mt
 * @property integer $status
 * @property integer $type
 * @property integer $service_id
 * @property string $start_date
 * @property string $end_date
 */
class Pushsms extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pushsms';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('mt', 'required'),
			array('status, type, service_id', 'numerical', 'integerOnly'=>true),
			array('start_date, end_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, mt, status, type, service_id, start_date, end_date', 'safe', 'on'=>'search'),
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
			'mt' => 'Mt',
			'status' => 'Status',
			'type' => 'Type',
			'service_id' => 'Service',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
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
		$criteria->compare('mt',$this->mt,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('type',$this->type);
		$criteria->compare('service_id',$this->service_id);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Pushsms the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

<?php

/**
 * This is the model class for table "notifi_question".
 *
 * The followings are the available columns in table 'notifi_question':
 * @property integer $id
 * @property integer $class_id
 * @property integer $subject_id
 * @property integer $count
 */
class NotifiQuestion extends CActiveRecord
{
    public $total;
    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'notifi_question';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('class_id, subject_id, count', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, class_id, subject_id, count', 'safe', 'on'=>'search'),
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
			'class_id' => 'Class',
			'subject_id' => 'Subject',
			'count' => 'Count',
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
		$criteria->compare('class_id',$this->class_id);
		$criteria->compare('subject_id',$this->subject_id);
		$criteria->compare('count',$this->count);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NotifiQuestion the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

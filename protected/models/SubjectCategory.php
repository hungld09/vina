<?php

/**
 * This is the model class for table "subject_category".
 *
 * The followings are the available columns in table 'subject_category':
 * @property integer $id
 * @property string $subject_name
 * @property string $subject_name_ascii
 * @property integer $status
 * @property integer $type
 * @property string $create_date
 * @property string $modify_date
 * @property string $description
 */
class SubjectCategory extends CActiveRecord
{

	private static $db2 = null;

	public function getDbConnection()
	{
		return self::$db2 = Yii::app()->db2;
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'subject_category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, type', 'numerical', 'integerOnly'=>true),
			array('subject_name, subject_name_ascii, description', 'length', 'max'=>200),
			array('create_date, modify_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, subject_name, subject_name_ascii, class_id, status, type, create_date, modify_date, description', 'safe', 'on'=>'search'),
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
			'subject_name' => 'Subject Name',
			'subject_name_ascii' => 'Subject Name Ascii',
			'status' => 'Status',
			'class_id' => 'Class Id',
			'type' => 'Type',
			'create_date' => 'Create Date',
			'modify_date' => 'Modify Date',
			'description' => 'Description',
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
		$criteria->compare('subject_name',$this->subject_name,true);
		$criteria->compare('subject_name_ascii',$this->subject_name_ascii,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('type',$this->type);
		$criteria->compare('class_id',$this->class_id);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('modify_date',$this->modify_date,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SubjectCategory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

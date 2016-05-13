<?php

/**
 * This is the model class for table "chapter".
 *
 * The followings are the available columns in table 'chapter':
 * @property integer $id
 * @property string $chapter_name
 * @property string $chapter_name_ascii
 * @property integer $subject_id
 * @property integer $class_id
 * @property integer $chapter_code
 */
class Chapter extends CActiveRecord
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
		return 'chapter';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('chapter_name, chapter_name_ascii', 'required'),
			array('subject_id, class_id, chapter_code', 'numerical', 'integerOnly'=>true),
			array('chapter_name, chapter_name_ascii', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, chapter_name, chapter_name_ascii, subject_id, class_id', 'safe', 'on'=>'search'),
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
			'chapter_name' => 'Chapter Name',
			'chapter_name_ascii' => 'Chapter Name Ascii',
			'subject_id' => 'Môn',
			'class_id' => 'Lớp',
			'chapter_code' => 'Mã chương',
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
		$criteria->compare('chapter_name',$this->chapter_name,true);
		$criteria->compare('chapter_name_ascii',$this->chapter_name_ascii,true);
		$criteria->compare('subject_id',$this->subject_id);
		$criteria->compare('class_id',$this->class_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Chapter the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

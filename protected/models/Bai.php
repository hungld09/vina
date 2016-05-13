<?php

/**
 * This is the model class for table "bai".
 *
 * The followings are the available columns in table 'bai':
 * @property integer $id
 * @property integer $class_id
 * @property integer $subject_id
 * @property integer $chapter_id
 * @property integer $bai_code
 * @property string $title
 * @property integer $week
 * @property integer $month
 * @property integer $precious
 */
class Bai extends CActiveRecord
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
		return 'bai';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('chapter_id, bai_code, title, week, month, precious', 'required'),
			array('class_id, subject_id, chapter_id, bai_code, week, month, precious', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, class_id, subject_id, chapter_id, bai_code, title, week, month, precious', 'safe', 'on'=>'search'),
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
			'class_id' => 'Lớp',
			'subject_id' => 'Môn',
			'chapter_id' => 'Chương',
			'bai_code' => 'Mã code',
			'title' => 'Tiêu đề',
			'week' => 'Tuần',
			'month' => 'Tháng',
			'precious' => 'Quí',
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
		$criteria->compare('chapter_id',$this->chapter_id);
		$criteria->compare('bai_code',$this->bai_code);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('week',$this->week);
		$criteria->compare('month',$this->month);
		$criteria->compare('precious',$this->precious);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Bai the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getClass($class = null, $type = false) {
		$array = array();
		$clas = Class1::model()->findAll();
		foreach($clas as $item){
			$array[$item->id] = $item->class_name;
		}
		if($class == null) {
			return $array;
		} else {
			if($type) {
				$className = Class1::model()->findByPk($class);
				if($className != null){
					return $className->class_name;
				}else{
					return null;
				}
			} else {
				return $array[$class] != null ? $array[$class] : 'UNKNOWN_STATUS_CODE';
			}
		}
	}

	public function getSubject($subject = null, $type = false) {
		$array = array();
		$subj = SubjectCategory::model()->findAll();
		foreach($subj as $item){
			$array[$item->id] = $item->subject_name;
		}
		if($subject == null) {
			return $array;
		} else {
			if($type) {
				$subjectName = SubjectCategory::model()->findByPk($subject);
				if($subjectName != null){
					return $subjectName->subject_name;
				}else{
					return null;
				}
			} else {
				return $array[$subject] != null ? $array[$subject] : 'UNKNOWN_STATUS_CODE';
			}
		}
	}
}

<?php

/**
 * This is the model class for table "question_import".
 *
 * The followings are the available columns in table 'question_import':
 * @property integer $id
 * @property integer $subject_id
 * @property integer $class_id
 * @property integer $level
 * @property string $question
 * @property string $answer_1
 * @property string $answer_2
 * @property string $answer_3
 * @property string $answer_4
 */
class QuestionImport extends CActiveRecord
{    public $test;
        private static $db3 = null;

	public function getDbConnection()
	{
		return self::$db3 = Yii::app()->db3;
	}
    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'question_import';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subject_id, class_id, level, question', 'required'),
			array('subject_id, class_id, level', 'numerical', 'integerOnly'=>true),
			array('answer_1, answer_2, answer_3, answer_4', 'length', 'max'=>500),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, subject_id, class_id, level, question, answer_1, answer_2, answer_3, answer_4', 'safe', 'on'=>'search'),
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
			'subject_id' => 'Subject',
			'class_id' => 'Class',
			'level' => 'Level',
			'question' => 'Question',
			'answer_1' => 'Answer 1',
			'answer_2' => 'Answer 2',
			'answer_3' => 'Answer 3',
			'answer_4' => 'Answer 4',
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
		$criteria->compare('subject_id',$this->subject_id);
		$criteria->compare('class_id',$this->class_id);
		$criteria->compare('level',$this->level);
		$criteria->compare('question',$this->question,true);
		$criteria->compare('answer_1',$this->answer_1,true);
		$criteria->compare('answer_2',$this->answer_2,true);
		$criteria->compare('answer_3',$this->answer_3,true);
		$criteria->compare('answer_4',$this->answer_4,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return QuestionImport the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
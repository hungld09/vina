<?php

/**
 * This is the model class for table "question_lib".
 *
 * The followings are the available columns in table 'question_lib':
 * @property integer $id
 * @property integer $class_id
 * @property integer $subject_id
 * @property integer $chapter_id
 * @property integer $bai_id
 * @property string $question
 * @property string $question_ascii
 * @property string $answer
 * @property integer $create_date
 * @property integer $status
 * @property integer $create_user_id
 * @property integer $update_user_id
 */
class QuestionLib extends CActiveRecord
{
	public $questionTag;

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
		return 'question_lib';
	}
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('class_id, subject_id, chapter_id, bai_id, question, question_ascii, answer, status, update_user_id, create_user_id, create_date', 'required'),
			array('class_id, subject_id, chapter_id, bai_id, create_date', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, class_id, subject_id, chapter_id, bai_id, question, question_ascii, answer, status, update_user_id, create_user_id, create_date', 'safe', 'on'=>'search'),
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
			'chapter_id' => 'Chapter',
			'bai_id' => 'Bai',
			'question' => 'Question',
			'question_ascii' => 'question_ascii',
			'answer' => 'Answer',
			'status' => 'Status',
			'create_date' => 'Create Date',
			'questionTag' => 'Chọn Tag',
			'create_user_id' => 'create_user_id',
			'update_user_id' => 'update_user_id',
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
		$criteria->compare('bai_id',$this->bai_id);
		$criteria->compare('question',$this->question,true);
		$criteria->compare('question_ascii',$this->question_ascii,true);
		$criteria->compare('answer',$this->answer,true);
		$criteria->compare('create_date',$this->create_date);
		$criteria->compare('status',0);
		$criteria->compare('create_user_id',$this->create_user_id);
		if(Yii::app()->user->id == 1 || Yii::app()->user->id == 3){
			$criteria->compare('update_user_id',$this->update_user_id);
		}else{
			$criteria->compare('update_user_id',Yii::app()->user->id);
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return QuestionLib the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

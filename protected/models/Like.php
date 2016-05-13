<?php

/**
 * This is the model class for table "like".
 *
 * The followings are the available columns in table 'like':
 * @property integer $id
 * @property integer $subscriber_id
 * @property integer $question_id
 * @property integer $answer_id
 * @property integer $comment_id
 */
class Like extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'like';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subscriber_id', 'required'),
			array('subscriber_id, question_id, answer_id, comment_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, subscriber_id, question_id, answer_id, comment_id', 'safe', 'on'=>'search'),
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
			'question_id' => 'Question',
			'answer_id' => 'Answer',
			'comment_id' => 'Comment',
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
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('answer_id',$this->answer_id);
		$criteria->compare('comment_id',$this->comment_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Like the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

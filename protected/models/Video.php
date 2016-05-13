<?php

/**
 * This is the model class for table "video".
 *
 * The followings are the available columns in table 'video':
 * @property integer $id
 * @property string $title
 * @property string $title_ascii
 * @property integer $subject_id
 * @property integer $chapter_id
 * @property string $image_thump
 * @property string $url_video
 * @property string $create_date
 */
class Video extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'video';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, title_ascii, subject_id, chapter_id, image_thump, url_video', 'required'),
			array('subject_id, chapter_id', 'numerical', 'integerOnly'=>true),
			array('title, title_ascii, image_thump, url_video', 'length', 'max'=>500),
			array('create_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, title_ascii, subject_id, chapter_id, image_thump, url_video, create_date', 'safe', 'on'=>'search'),
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
			'title' => 'Tiêu đề',
			'title_ascii' => 'title_ascii',
			'subject_id' => 'Subject',
			'chapter_id' => 'Chapter',
			'image_thump' => 'Image Thump',
			'url_video' => 'Url Video',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('title_ascii',$this->title_ascii,true);
		$criteria->compare('subject_id',$this->subject_id);
		$criteria->compare('chapter_id',$this->chapter_id);
		$criteria->compare('image_thump',$this->image_thump,true);
		$criteria->compare('url_video',$this->url_video,true);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Video the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

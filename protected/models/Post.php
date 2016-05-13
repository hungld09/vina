<?php

/**
 * This is the model class for table "tbl_post".
 *
 * The followings are the available columns in table 'tbl_post':
 * @property integer $id
 * @property string $title
 * @property string $tag
 * @property integer $status
 * @property string $create_time
 * @property integer $author_id
 */
class Post extends CActiveRecord
{
	public function getUrl()
    {
        return Yii::app()->createUrl('post/view', array(
            'id'=>$this->id,
            'title'=>$this->title,
        ));
    }
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_post';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, status, create_time, author_id', 'required'),
			array('status, author_id', 'numerical', 'integerOnly'=>true),
			array('title, tag', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, tag, status, create_time, author_id', 'safe', 'on'=>'search'),
		);
	}

//	public function rules()
//	{
//	    return array(
//	        array('title, content, status', 'required'),
//	        array('title', 'length', 'max'=>128),
//	        array('status', 'in', 'range'=>array(1,2,3)),
//	        array('tags', 'match', 'pattern'=>'/^[\w\s,]+$/',
//	            'message'=>'Tags can only contain word characters.'),
//	        array('tags', 'normalizeTags'),
//	 
//	        array('title, status', 'safe', 'on'=>'search'),
//	    );
//	}
	
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
			'title' => 'Title',
			'tag' => 'Tag',
			'status' => 'Status',
			'create_time' => 'Create Time',
			'author_id' => 'Author',
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
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('author_id',$this->author_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Post the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

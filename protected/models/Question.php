<?php

/**
 * This is the model class for table "question".
 *
 * The followings are the available columns in table 'question':
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $category_id
 * @property string $title_ascii
 * @property string $class_id
 * @property integer $subscriber_id
 * @property string $create_date
 * @property string $modify_date
 * @property integer $status
 * @property integer $type
 *
 * The followings are the available model relations:
 * @property Subscriber $subscriber
 */
class Question extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'question';
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
			array('category_id, subscriber_id, teacher_id, status, type, count_like, count_comment', 'numerical', 'integerOnly'=>true),
			array('title, title_ascii', 'length', 'max'=>200),
			array('class_id', 'length', 'max'=>45),
			array('content, create_date, modify_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, content, category_id, title_ascii, level_id, class_id, loop, subscriber_id, teacher_id, create_date, modify_date, status, type, count_like, count_comment', 'safe', 'on'=>'search'),
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
			'subscriber' => array(self::BELONGS_TO, 'Subscriber', 'subscriber_id'),
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
			'content' => 'Content',
			'category_id' => 'Category',
			'title_ascii' => 'Title Ascii',
			'class_id' => 'Class',
			'subscriber_id' => 'Subscriber',
			'create_date' => 'Create Date',
			'modify_date' => 'Modify Date',
			'status' => 'Status',
			'type' => 'Type',
			'loop' => 'Loop',
			'level_id' => 'Level Id',
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
		$criteria->compare('content',$this->content,true);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('title_ascii',$this->title_ascii,true);
		$criteria->compare('class_id',$this->class_id,true);
		$criteria->compare('subscriber_id',$this->subscriber_id);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('modify_date',$this->modify_date,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('type',$this->type);
		$criteria->compare('level_id',$this->level_id);
		$criteria->compare('loop',$this->loop);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Question the static model class
	 */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getQuestion($limit = 4, $offset = 0, $type = 0, $status = 0, $userId, $class = 0, $subject = 0)
    {
//        $sql = 'm.status= 1';
        $sql = 't.id is not null';
        if ($status != 0) {
            if($status == 6){
                $sql .= ' AND (t.status = 6 or t.status = 3)';
            }else{
                if($status == 1 || $status == 3){
                    $sql .= ' AND t.status = ' . $status;
                }  else {
                    $sql .= ' AND a.status = ' . $status;
                    $sql .= ' AND t.status = ' . $status;
                }
                
            }
        }
        if ($type == 1) {
            $sql .= ' AND t.subscriber_id = ' . $userId;
        }
        if ($type == 2) {
			if ($status != 1) { 
				$sql .= ' AND a.subscriber_id = '.$userId;
//				$sql .= ' AND a.status = '.$status;
			}
        }
        if ($class != 0) {
            $sql .= ' AND t.class_id = ' . $class;
        }
        if ($subject != 0) {
            $sql .= ' AND t.category_id = ' . $subject;
        }
//        echo $sql;die;
        if($status == 1){
            $result = Yii::app()->db->createCommand()
                ->select('t.id, t.title,t.content, t.category_id, t.class_id, t.subscriber_id, t.create_date, t.modify_date, t.status, t.level_id, t.count_like, t.count_comment')
                ->from('question t')
                ->where($sql)
//                ->group('a.question_id')
                ->order('t.create_date desc')
                ->limit($limit)
                ->offset($offset)
                ->queryAll();
        }else{
            $result = Yii::app()->db->createCommand()
                ->select('t.id, t.title,t.content, t.category_id, t.class_id, t.subscriber_id, t.create_date, t.modify_date, t.status, t.level_id, t.count_like, t.count_comment')
                ->from('question t')
                ->join('answer a', 't.id = a.question_id')
                ->where($sql)
                //            ->group('m.question_id')
                ->order('t.create_date desc')
                ->limit($limit)
                ->offset($offset)
                ->queryAll();
        }
        return array(
            'data' => $result
        );
    }
    public function getQuestionBank($tag_id){
        $sql = 't.tag_id in ('.$tag_id.')';
        $result = Yii::app()->db->createCommand()
            ->select('t.id, t.question_id')
            ->from('tag_question_mapping t')
            ->where($sql)
            ->queryAll();
        return array(
            'data' => $result
        );
    }
}

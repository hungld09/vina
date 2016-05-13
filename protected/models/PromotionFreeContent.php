<?php

/**
 * This is the model class for table "promotion_free_content".
 *
 * The followings are the available columns in table 'promotion_free_content':
 * @property integer $id
 * @property integer $subscriber_id
 * @property integer $total
 * @property integer $type
 * @property string $created_date
 */
class PromotionFreeContent extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'promotion_free_content';
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
			array('subscriber_id, total', 'numerical', 'integerOnly'=>true),
			array('created_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, subscriber_id, total, type, created_date', 'safe', 'on'=>'search'),
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
			'total' => 'Total',
			'type' => 'Type',
			'created_date' => 'Created Date',
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
		$criteria->compare('total',$this->total);
		$criteria->compare('type',$this->type);
		$criteria->compare('created_date',$this->created_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PromotionFreeContent the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

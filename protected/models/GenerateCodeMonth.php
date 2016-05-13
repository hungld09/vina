<?php

/**
 * This is the model class for table "generate_code_month".
 *
 * The followings are the available columns in table 'generate_code_month':
 * @property integer $id
 * @property string $card_seria
 * @property string $card_code
 * @property integer $status
 * @property integer $type
 * @property string $create_date
 */
class GenerateCodeMonth extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'generate_code_month';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('card_seria, card_code, create_date', 'required'),
			array('status, type', 'numerical', 'integerOnly'=>true),
			array('card_seria, card_code', 'length', 'max'=>25),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, card_seria, card_code, status, type, create_date', 'safe', 'on'=>'search'),
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
			'card_seria' => 'Card Seria',
			'card_code' => 'Card Code',
			'status' => 'Status',
			'type' => 'Type',
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
		$criteria->compare('card_seria',$this->card_seria,true);
		$criteria->compare('card_code',$this->card_code,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('type',$this->type);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GenerateCodeMonth the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

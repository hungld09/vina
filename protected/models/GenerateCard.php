<?php

/**
 * This is the model class for table "generate_card".
 *
 * The followings are the available columns in table 'generate_card':
 * @property integer $id
 * @property string $card_seria
 * @property string $card_code
 * @property double $amount
 * @property integer $status
 * @property string $create_date
 * @property string $modify_date
 */
class GenerateCard extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'generate_card';
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
			array('status', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('card_seria, card_code', 'length', 'max'=>25),
			array('modify_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, card_seria, card_code, type, amount, status, create_date, modify_date', 'safe', 'on'=>'search'),
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
			'amount' => 'Amount',
			'status' => 'Status',
			'type' => 'Type',
			'create_date' => 'Create Date',
			'modify_date' => 'Modify Date',
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
		$criteria->compare('amount',$this->amount);
		$criteria->compare('status',$this->status);
		$criteria->compare('type',$this->type);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('modify_date',$this->modify_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GenerateCard the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

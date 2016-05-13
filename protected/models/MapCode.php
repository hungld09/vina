<?php

/**
 * This is the model class for table "map_code".
 *
 * The followings are the available columns in table 'map_code':
 * @property integer $id
 * @property integer $subscriber_id
 * @property integer $is_active
 * @property integer $type
 * @property integer $total
 * @property string $create_date
 * @property string $expiry_date
 */
class MapCode extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'map_code';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('subscriber_id', 'required'),
            array('subscriber_id, is_active, type, total', 'numerical', 'integerOnly' => true),
            array('create_date, expiry_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, subscriber_id, is_active, type, total, create_date, expiry_date', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'subscriber_id' => 'Subscriber',
            'is_active' => 'Is Active',
            'type' => 'Type',
            'total' => 'Total',
            'create_date' => 'Create Date',
            'expiry_date' => 'Expiry Date',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('subscriber_id', $this->subscriber_id);
        $criteria->compare('is_active', $this->is_active);
        $criteria->compare('type', $this->type);
        $criteria->compare('total', $this->total);
        $criteria->compare('create_date', $this->create_date, true);
        $criteria->compare('expiry_date', $this->expiry_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MapCode the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function activeCode40($subscriber_id) {
        $checkcode40 = MapCode::model()->findByAttributes(array('subscriber_id' => $subscriber_id, 'type' => 1));
        if ($checkcode40 == null) {
            $mapcode = new MapCode();
            $mapcode->subscriber_id = $subscriber_id;
            $mapcode->is_active = 1;
            $mapcode->total = 0;
            $mapcode->type = 1;
            $mapcode->create_date = date('Y-m-d H:i:s');
            $mapcode->expiry_date = date('Y-m-d H:i:s', (time() + 30 * 60 * 60 * 24));
            $mapcode->save();
        }else{
            $time = date('Y-m-d H:i:s', (strtotime($checkcode40->expiry_date)+ 30 * 60 * 60 * 24));
            $checkcode40->is_active = 1;
            $checkcode40->expiry_date = $time;
            $checkcode40->save();
        }
    }

}

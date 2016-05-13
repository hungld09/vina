<?php

/**
 * This is the model class for table "service".
 *
 * The followings are the available columns in table 'service':
 * @property integer $id
 * @property string $display_name
 * @property string $code_name
 * @property string $description
 * @property integer $status
 * @property double $price
 * @property integer $using_days
 * @property integer $free_content
 * @property integer $default
 * @property string $create_date
 * @property integer $promotion
 * @property string $promotion_start
 * @property string $promotion_end
 * @property integer $gift
 */
class Service extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'service';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('display_name, code_name', 'required'),
            array('status, using_days, free_content, type, promotion, gift', 'numerical', 'integerOnly' => true),
            array('price, question_price', 'numerical'),
            array('display_name, code_name', 'length', 'max' => 45),
            array('description', 'length', 'max' => 200),
            array('create_date, promotion_start, promotion_end', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, display_name, code_name, description, status, price, question_price, using_days, free_content, type, create_date, promotion, promotion_start, promotion_end, gift', 'safe', 'on' => 'search'),
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
            'display_name' => 'Service Name',
            'code_name' => 'Code Name',
            'description' => 'Mô tả',
            'status' => 'Trạng thái',
            'price' => 'Giá tiền (coin)',
            'using_days' => 'Using Days',
            'free_content' => 'Free Content',
            'type' => 'Service type',
            'create_date' => 'Create Date',
            'promotion' => 'Khuyến mại',
            'promotion_start' => 'Thời gian bắt đầu',
            'promotion_end' => 'Thời gian kết thúc',
            'gift' => '% khuyến mại',
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
        $criteria->compare('display_name', $this->display_name, true);
        $criteria->compare('code_name', $this->code_name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('price', $this->price);
        $criteria->compare('using_days', $this->using_days);
        $criteria->compare('free_content', $this->free_content);
        $criteria->compare('default', $this->default);
        $criteria->compare('create_date', $this->create_date, true);
        $criteria->compare('promotion', $this->promotion);
        $criteria->compare('promotion_start', $this->promotion_start, true);
        $criteria->compare('promotion_end', $this->promotion_end, true);
        $criteria->compare('gift', $this->gift);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Service the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getService() {
        $dependency = new CDbCacheDependency('SELECT 1');
        $service = Service::model()->cache(365 * 24 * 60 * 60, $dependency)->findAllByAttributes(array('is_deleted' => 0));
        return $service;
    }

}

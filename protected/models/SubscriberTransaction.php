
<?php

/**
 * This is the model class for table "subscriber_transaction".
 *
 * The followings are the available columns in table 'subscriber_transaction':
 * @property integer $id
 * @property integer $service_id
 * @property string $vnp_username
 * @property string $vnp_ip
 * @property integer $subscriber_id
 * @property integer $status
 * @property string $description
 * @property integer $cost
 * @property string $channel_type
 * @property integer $using_type
 * @property integer $purchase_type
 * @property string $error_code
 */
class SubscriberTransaction extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'subscriber_transaction';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('service_id, subscriber_id, channel_type', 'required'),
            array('service_id, subscriber_id, status, req_id, using_type, purchase_type', 'numerical', 'integerOnly' => true),
            array('cost', 'numerical'),
            array('vnp_username, vnp_ip', 'length', 'max' => 30),
            array('description', 'length', 'max' => 200),
            array('channel_type, error_code', 'length', 'max' => 20),
            array('create_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, service_id, vnp_username, vnp_ip, subscriber_id, status, description, cost, channel_type, using_type, purchase_type, error_code, create_date', 'safe', 'on' => 'search'),
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
            'service_id' => 'Service',
            'vnp_username' => 'Vnp Username',
            'vnp_ip' => 'Vnp Ip',
            'subscriber_id' => 'Subscriber',
            'status' => 'Status',
            'description' => 'Description',
            'cost' => 'Cost',
            'channel_type' => 'Channel Type',
            'using_type' => 'Using Type',
            'purchase_type' => 'Purchase Type',
            'error_code' => 'Error Code',
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
        $criteria->compare('service_id', $this->service_id);
        $criteria->compare('vnp_username', $this->vnp_username, true);
        $criteria->compare('vnp_ip', $this->vnp_ip, true);
        $criteria->compare('subscriber_id', $this->subscriber_id);
        $criteria->compare('status', $this->status);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('cost', $this->cost);
        $criteria->compare('channel_type', $this->channel_type, true);
        $criteria->compare('using_type', $this->using_type);
        $criteria->compare('purchase_type', $this->purchase_type);
        $criteria->compare('error_code', $this->error_code, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SubscriberTransaction the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function getMaxTransactionId() {
        $result = Yii::app()->db->createCommand()
                ->selectDistinct('t.id')
                ->from('subscriber_transaction as t')
                ->limit(0, 1)
                ->order('t.id DESC')
                ->queryScalar();
        if (!$result) {
            $result = 1;
        }
        return $result;
    }

    public static function getTypeName($purchae_type) {
        switch ($purchae_type) {
            case SubscriberTransaction::PURCHASE_TYPE_NEW:
                return "Đăng ký";
            case SubscriberTransaction::PURCHASE_TYPE_EXTEND_TIME:
                return "Gia hạn";
            case SubscriberTransaction::PURCHASE_TYPE_RETRY_EXTEND:
                return "Truy thu";
            case SubscriberTransaction::PURCHASE_TYPE_CANCEL:
                return "Hủy";
            case SubscriberTransaction::PURCHASE_TYPE_FORCE_CANCEL:
                return "Bị hủy";
            case SubscriberTransaction::PURCHASE_TYPE_PENDING:
                return "Tạm dừng";
            case SubscriberTransaction::PURCHASE_TYPE_RESTORE:
                return "Khôi phục";
        }
    }

}

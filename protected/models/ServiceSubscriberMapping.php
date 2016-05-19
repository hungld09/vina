
<?php

/**
 * This is the model class for table "service_subscriber_mapping".
 *
 * The followings are the available columns in table 'service_subscriber_mapping':
 * @property string $id
 * @property integer $service_id
 * @property integer $promotion
 * @property integer $subscriber_id
 * @property string $description
 * @property string $activate_date
 * @property string $expiry_date
 * @property integer $is_active
 * @property string $create_date
 * @property string $modify_date
 * @property integer $is_deleted
 * @property string $pending_date
 * @property integer $view_count
 * @property integer $sent_notification
 * @property integer $recur_retry_times
 * @property integer $partner_id
 * @property integer $vtv
 *
 * The followings are the available model relations:
 * @property Service $service
 * @property Subscriber $subscriber
 * @property Partner $partner
 */
class ServiceSubscriberMapping extends CActiveRecord
{
    const SERVICE_STATUS_INACTIVE = 0;
    const SERVICE_STATUS_ACTIVE = 1;
    const SERVICE_STATUS_PENDING = 2;
    const SERVICE_STATUS_EXTEND_PENDING = 3;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'service_subscriber_mapping';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('service_id, subscriber_id, activate_date, expiry_date, create_date', 'required'),
            array('service_id, promotion, subscriber_id, is_active, is_deleted, view_count, sent_notification, recur_retry_times, partner_id, vtv', 'numerical', 'integerOnly'=>true),
            array('description', 'length', 'max'=>1000),
            array('modify_date, pending_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, service_id, promotion, subscriber_id, description, activate_date, expiry_date, is_active, create_date, modify_date, is_deleted, pending_date, view_count, sent_notification, recur_retry_times, partner_id, vtv', 'safe', 'on'=>'search'),
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
            'service' => array(self::BELONGS_TO, 'Service', 'service_id'),
            'subscriber' => array(self::BELONGS_TO, 'Subscriber', 'subscriber_id'),
            'partner' => array(self::BELONGS_TO, 'Partner', 'partner_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'service_id' => 'Service',
            'promotion' => 'Promotion',
            'subscriber_id' => 'Subscriber',
            'description' => 'Description',
            'activate_date' => 'Activate Date',
            'expiry_date' => 'Expiry Date',
            'is_active' => 'Is Active',
            'create_date' => 'Create Date',
            'modify_date' => 'Modify Date',
            'is_deleted' => 'Is Deleted',
            'pending_date' => 'Pending Date',
            'view_count' => 'View Count',
            'sent_notification' => 'Sent Notification',
            'recur_retry_times' => 'Recur Retry Times',
            'partner_id' => 'Partner',
            'vtv' => 'Vtv',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('service_id',$this->service_id);
        $criteria->compare('promotion',$this->promotion);
        $criteria->compare('subscriber_id',$this->subscriber_id);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('activate_date',$this->activate_date,true);
        $criteria->compare('expiry_date',$this->expiry_date,true);
        $criteria->compare('is_active',$this->is_active);
        $criteria->compare('create_date',$this->create_date,true);
        $criteria->compare('modify_date',$this->modify_date,true);
        $criteria->compare('is_deleted',$this->is_deleted);
        $criteria->compare('pending_date',$this->pending_date,true);
        $criteria->compare('view_count',$this->view_count);
        $criteria->compare('sent_notification',$this->sent_notification);
        $criteria->compare('recur_retry_times',$this->recur_retry_times);
        $criteria->compare('partner_id',$this->partner_id);
        $criteria->compare('vtv',$this->vtv);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ServiceSubscriberMapping the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function isRegisteredInThreeMonth($subscriber_id){
        $date = date("Y-m-d H:i:s");
        $previewDate = strtotime($date."- 3 months");
        $previewDate = date("Y-m-d H:i:s",$previewDate);
//        $sqlString ="select * from service_subscriber_mapping where subscriber_id = '$subscriber_id' and create_date > '$previewDate'";
        $sqlString ="select * from service_subscriber_mapping where subscriber_id = '$subscriber_id' and (create_date > '$previewDate' or (modify_date is not null and modify_date > '$previewDate' ))";
        $serviceMapping = ServiceSubscriberMapping::model()->findAllBySql($sqlString, array());
        if(count($serviceMapping) > 0) return 1;
        else return 0;
    }
}
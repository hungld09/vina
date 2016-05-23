<?php

    /**
     * This is the model class for table "videos".
     *
     * The followings are the available columns in table 'videos':
     *
     * @property integer $id
     * @property string  $name
     * @property string  $thumb
     * @property integer $chapter_id
     * @property integer $subject_id
     * @property integer $class_id
     * @property string  $created
     * @property integer $status
     */
    class Videos extends CActiveRecord
    {
        /**
         * @return string the associated database table name
         */
        public function tableName()
        {
            return 'videos';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                array('name', 'required'),
                array('chapter_id, subject_id, class_id, status', 'numerical', 'integerOnly' => TRUE),
                array('name, thumb', 'length', 'max' => 255),
                array('created', 'safe'),
                // The following rule is used by search().
                // @todo Please remove those attributes that should not be searched.
                array('id, name, thumb, chapter_id, subject_id, class_id, created, status', 'safe', 'on' => 'search'),
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            // NOTE: you may need to adjust the relation name and the related
            // class name for the relations automatically generated below.
            return array();
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'id'         => 'ID',
                'name'       => 'Tiêu đề',
                'thumb'      => 'Thumbnail',
                'chapter_id' => 'Chương',
                'subject_id' => 'Môn học',
                'class_id'   => 'Lớp',
                'created'    => 'Ngày tạo',
                'status'     => 'Trạng thái',
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

            $criteria = new CDbCriteria;

            $criteria->compare('id', $this->id);
            $criteria->compare('name', $this->name, TRUE);
            $criteria->compare('thumb', $this->thumb, TRUE);
            $criteria->compare('chapter_id', $this->chapter_id);
            $criteria->compare('subject_id', $this->subject_id);
            $criteria->compare('class_id', $this->class_id);
            $criteria->compare('created', $this->created, TRUE);
            $criteria->compare('status', $this->status);

            return new CActiveDataProvider($this, array(
                'criteria' => $criteria,
                'sort'     => array(
                    'defaultOrder' => 'id DESC',
                ),
            ));
        }

        /**
         * Returns the static model of the specified AR class.
         * Please note that you should have this exact method in all your CActiveRecord descendants!
         *
         * @param string $className active record class name.
         *
         * @return Videos the static model class
         */
        public static function model($className = __CLASS__)
        {
            return parent::model($className);
        }
        /**
         * Get image url
         *
         * @return string
         */
        public function getImageUrl()
        {
            $dir_root = '../';

            return ($this->thumb != '' && file_exists($dir_root . $this->thumb)) ? CHtml::image($dir_root . $this->thumb, $this->name, array("width" => "100", "height" => "60", "title" => $this->name)) : CHtml::image(Yii::app()->theme->baseUrl . "/images/no_img.png", "", array("width" => "100", "height" => "60", "title" => ""));
        }
    }

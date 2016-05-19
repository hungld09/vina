<?php

    /**
     * This is the model class for table "blog".
     *
     * The followings are the available columns in table 'blog':
     *
     * @property integer $id
     * @property string  $title
     * @property string  $title_code
     * @property string  $content
     * @property string  $description
     * @property string  $keywords
     * @property string  $image_url
     * @property integer $create_date
     * @property integer $type
     * @property integer $status
     * @property integer $category_id
     */
    class Blog extends CActiveRecord
    {
        public $status;

        /**
         * @return string the associated database table name
         */
        public function tableName()
        {
            return 'blog';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                array('title, title_code, content, create_date, type, category_id', 'required'),
                array('create_date, type, status, category_id', 'numerical', 'integerOnly' => TRUE),
                array('title, title_code, image_url', 'length', 'max' => 255),
                // The following rule is used by search().
                // @todo Please remove those attributes that should not be searched.
                array('id, title, title_code, content, description, keywords, image_url, create_date, type, status, category_id', 'safe', 'on' => 'search'),
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
                'id'          => 'ID',
                'title'       => 'Tiêu đề',
                'title_code'  => 'Title Code',
                'content'     => 'Mô tả',
                'description' => 'Description',
                'keywords'    => 'Keywords',
                'image_url'   => 'Thumbnail',
                'create_date' => 'Create Date',
                'type'        => 'Type',
                'status'      => 'Trạng thái',
                'category_id' => 'Chuyên mục',
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

            $criteria->order = 'id desc';
            $criteria->compare('id', $this->id);
            $criteria->compare('title', $this->title, TRUE);
            $criteria->compare('title_code', $this->title_code, TRUE);
            $criteria->compare('content', $this->content, TRUE);
            $criteria->compare('description', $this->description, TRUE);
            $criteria->compare('keywords', $this->keywords, TRUE);
            $criteria->compare('image_url', $this->image_url, TRUE);
            $criteria->compare('create_date', $this->create_date);
            $criteria->compare('type', $this->type);
            $criteria->compare('status', $this->status);
            $criteria->compare('category_id', $this->category_id);

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
         * @return Blog the static model class
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

            return ($this->image_url != '' && file_exists($dir_root . $this->image_url)) ? CHtml::image($dir_root . $this->image_url, $this->title, array("width" => "100", "height" => "60", "title" => $this->title)) : CHtml::image(Yii::app()->theme->baseUrl . "/images/no_img.png", "", array("width" => "100", "height" => "60", "title" => ""));
        }
    }

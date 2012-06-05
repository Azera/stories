<?php

/**
 * This is the model class for table "comment".
 *
 * The followings are the available columns in table 'comment':
 * @property string $id
 * @property string $story_id
 * @property string $author_id
 * @property string $content
 * @property string $content_html
 * @property string $is_published
 * @property string $reported_user
 * @property string $reported_time
 * @property string $reported_note
 * @property string $reviewed_user
 * @property string $reviewed_note
 * @property string $reviewed_time
 * @property string $create_user
 * @property string $create_time
 * @property string $update_user
 * @property string $update_time
 */
class Comment extends AuditActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Story the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content', 'required'),
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
			'story'=>array(self::BELONGS_TO, 'Story', 'story_id'),
			'author'=>array(self::BELONGS_TO, 'User', 'author_id'),
			'reporter'=>array(self::BELONGS_TO, 'User', 'reported_user'),
			'reviewer'=>array(self::BELONGS_TO, 'User', 'reviewed_user'),
			'creater'=>array(self::BELONGS_TO, 'User', 'created_user'),
			'updater'=>array(self::BELONGS_TO, 'User', 'updated_user'),
		);
	}

	/**
	 * @return array named scopes.
	 */
	public function scopes()
	{
		return array(
			'published'=>array(
				'condition'=>'is_published=1',
			),
			'unpublished'=>array(
				'condition'=>'is_published=0',
            ),
			'reported'=>array(
				'condition'=>'reported_user IS NOT NULL AND reviewed_user IS NULL',
            ),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'story_id' => 'Story',
			'author_id' => 'Author',
			'content' => 'Content',
			'content_html' => 'Content HTML',
			'is_published' => 'Published',
			'reported_user' => 'Reported User',
			'reported_time' => 'Reported Time',
			'reported_note' => 'Reported Note',
			'reviewed_user' => 'Reviewed User',
			'reviewed_note' => 'Reviewed Note',
			'reviewed_time' => 'Reviewed Time',
			'create_user' => 'Create User',
			'create_time' => 'Create Time',
			'update_user' => 'Update User',
			'update_time' => 'Update Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('content',$this->content,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Prepares author_id and is_approved for new records
	 */
	protected function beforeValidate()
	{
		if($this->isNewRecord)
		{
			// Set author
			$this->author_id = Yii::app()->user->id;
			$this->is_published = 0; // TODO: Depends on user points or admin
		}
		return parent::beforeValidate();
	}

}
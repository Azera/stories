<?php

/**
 * This is the model class for table "story".
 *
 * The followings are the available columns in table 'story':
 * @property string $id
 * @property string $author_id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property string $content_html
 * @property string $tags
 * @property string $is_published
 * @property string $read_count
 * @property string $rating_count_1
 * @property string $rating_count_2
 * @property string $rating_count_3
 * @property string $rating_count_4
 * @property string $rating_count_5
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
class Story extends AuditActiveRecord
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
		return 'story';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, description, content', 'required'),
			array('title', 'length', 'max'=>50),
			array('description, tags', 'length', 'max'=>250),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('title, description, content, tags', 'safe', 'on'=>'search'),
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
			'author'=>array(self::BELONGS_TO, 'User', 'author_id'),
			'reporter'=>array(self::BELONGS_TO, 'User', 'reported_user'),
			'reviewer'=>array(self::BELONGS_TO, 'User', 'reviewed_user'),
			'creater'=>array(self::BELONGS_TO, 'User', 'created_user'),
			'updater'=>array(self::BELONGS_TO, 'User', 'updated_user'),

			'comments'=>array(self::HAS_MANY, 'Comment', 'story_id',
				'order'=>'comments.create_time DESC',
				'with'=>'author',
				'together'=>false,
				'condition'=>'comments.is_published=1',
			),
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
			'author_id' => 'Author',
			'title' => 'Title',
			'description' => 'Description',
			'content' => 'Content',
			'content_html' => 'Content HTML',
			'tags' => 'Tags',
			'is_published' => 'Published',
			'read_count' => 'Read Count',
			'rating_count_1' => 'Rating Count 1',
			'rating_count_2' => 'Rating Count 2',
			'rating_count_3' => 'Rating Count 3',
			'rating_count_4' => 'Rating Count 4',
			'rating_count_5' => 'Rating Count 5',
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

		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('tags',$this->tags,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Prepares author_id and is_published for new records
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

	protected function beforeSave() {
		// Parse story content int HRML format
		$parser=new CMarkdownParser();
		$this->content_html = $parser->transform(CHtml::encode($this->content));

		parent::beforeSave();
	}
	/**
	 * return the total rating for the story. Will return "n/a" if the
	 * story has received less than 5 ratings
	 */
	public function getTotalRating()
	{
		$totalvotes = $this->rating_count_1 +
				$this->rating_count_2 +
				$this->rating_count_3 +
				$this->rating_count_4 +
				$this->rating_count_5;
		if($totalvotes < 5) return 'n/a';
		return
			((1 * $this->rating_count_1) +
			(2 * $this->rating_count_2) +
			(3 * $this->rating_count_3) +
			(4 * $this->rating_count_4) +
			(5 * $this->rating_count_5)) / ($totalvotes);
	}

	/**
	 * Adds a new comment to this story.
	 * This method will set story_id to this story, authoer_id to current
	 * user's id, and is_published according to the current user's points
	 * or is_admin status
	 * @param Comment comment to be added
	 * @return boolean whether the comment is saved successfully
	 */
	public function addComment(Comment $comment)
	{
		$comment->story_id = $this->id;
		$comment->author_id = Yii::app()->user->id;
		$comment->is_published = 0; // TODO: Depends on user points or is_admin
		return $comment->save();
	}

	/**
	 * Determines if a story can be reported. i.e. is not reported already,
	 * and has not been reviewed.
	 * @return bool Wether or not the story can be reported
	 */
	public function getCanBeReported()
	{
		return (NULL == $this->reported_user) and (NULL == $this->reviewed_user);
	}
}
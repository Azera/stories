<?php

class StoryController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			// Actions allowed for ALL users
			array('allow',
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			// Actions allowed for authenticated users
			// Additional checks are in place in actions (i.e. points system)
			array('allow',
				'actions'=>array('create', 'newcomment', 'rate', 'report'),
				'users'=>array('@'),
			),
			// deny all users
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model = $this->loadModel($id);

		// Increase the view counter
		// Because this is very simple, and needs to be as fast as possible, and
		// in order to bypass our audit fields, we're using raw sql here
		$sql = 'UPDATE '. $model->tableName() .' SET read_count=read_count+1 WHERE id=:id';
		Yii::app()->db->createCommand($sql)->execute(array(':id'=>$model->id));


		$comment=new Comment();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Comment']))
		{
			$comment->attributes=$_POST['Comment'];
			$comment->story_id = $id;
			if($comment->save())
			{
				if(!$comment->is_published)
					Yii::app()->user->setFlash('commentSubmitted','Thank you for your comment. Your comment will be posted once it is approved.');
				$this->refresh();
			}
		}


		$this->render('view', array(
			'model'=>$model,
			'comment'=>$comment,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Story;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Story']))
		{
			$model->attributes=$_POST['Story'];
			if($model->save())
				$this->redirect(array('view', 'id'=>$model->id));
		}

		$this->render('create', array('model'=>$model));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Story']))
		{
			$model->attributes=$_POST['Story'];
			if($model->save())
				$this->redirect(array('view', 'id'=>$model->id));
		}

		$this->render('update', array('model'=>$model));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Story');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Story::model()->published()->with('author', 'reporter', 'reviewer', 'comments')->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');

		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='story-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}

<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	'preload'=>array('log'),

	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.extensions.yii-mail.*',
	),

	'modules'=>array(
		// Gii enabled in non-production mode only
		'gii'=>defined('ENV_PROD')?NULL:array(
			'class'=>'system.gii.GiiModule',
			'password'=>false,
			'ipFilters'=>array('127.0.0.1','::1'),
		),
	),
	'components'=>array(
		'user'=>array(
			'allowAutoLogin'=>true,
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'db'=>CMap::mergeArray(array(
			'charset'=>'utf8',
			'schemaCachingDuration'=>defined('ENV_PROD')?60*60:0,
		), require(dirname(__FILE__).'/db.php')),
		'errorHandler'=>array(
			'errorAction'=>'site/error',
		),
		'request'=>array(
			'enableCsrfValidation'=>true,
		),
		'mail'=>CMap::mergeArray(array(
			'class' => 'application.extensions.yii-mail.YiiMail',
			'viewPath' => 'application.views.mail',
			'logging' => true,
			'dryRun' => false
		), require(dirname(__FILE__).'/mail.php')),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),
	'params'=>require(dirname(__FILE__).'/params.php'),
);
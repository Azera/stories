<?php

// Uncomment this for production environment
// define('ENV_PROD', true);
defined('ENV_PROD') or define('YII_DEBUG', true);
defined('ENV_PROD') or define('YII_TRACE_LEVEL', 3);

require_once(dirname(__FILE__).'/../yii-1.1.10.r3566/framework/yii.php');
Yii::createWebApplication(dirname(__FILE__).'/../protected/config/main.php')->run();

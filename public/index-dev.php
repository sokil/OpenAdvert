<?php

date_default_timezone_set('Europe/Kiev');
mb_internal_encoding('UTF8');

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('PUBLIC_PATH', realpath(APPLICATION_PATH . '/../public'));

$yii = APPLICATION_PATH . '/../vendor/yiisoft/yii/framework/yii.php';
$config = APPLICATION_PATH . '/config/development.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

// init PSR-0 loader from composer
require_once APPLICATION_PATH . '/../vendor/autoload.php';

// start app
require_once($yii);
Yii::createWebApplication($config)->run();

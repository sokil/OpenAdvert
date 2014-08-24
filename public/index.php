<?php

date_default_timezone_set('Europe/Kiev');
mb_internal_encoding('UTF8');

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('PUBLIC_PATH', realpath(APPLICATION_PATH . '/../public'));

$yii = APPLICATION_PATH . '/../vendor/yiisoft/yii/framework/yiilite.php';
$config = APPLICATION_PATH . '/config/production.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',false);

// init PSR-0 loader from composer
require_once APPLICATION_PATH . '/../vendor/autoload.php';

// start app
require_once($yii);
Yii::createWebApplication($config)->run();

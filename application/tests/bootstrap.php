<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';

define('APPLICATION_PATH', realpath(__DIR__ . '/..'));
define('PUBLIC_PATH', realpath(APPLICATION_PATH . '/../public'));

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

// init PSR-0 loader from composer
$loader = require_once APPLICATION_PATH . '/../vendor/autoload.php';
$loader->add('MongoAdvertDb\\', __DIR__ . '/unit/application/models/');
$loader->add('Report\\', __DIR__ . '/unit/application/models/');

Yii::createWebApplication($config);

// mongo fixture
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fixture.php');

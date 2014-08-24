<?php

date_default_timezone_set('Europe/Kiev');
mb_internal_encoding('UTF8');

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('PUBLIC_PATH', realpath(APPLICATION_PATH . '/../public'));

// init PSR-0 loader from composer
require_once APPLICATION_PATH . '/../vendor/autoload.php';

// change the following paths if necessary
$yiic=dirname(__FILE__).'/../framework/yiic.php';
$config=dirname(__FILE__).'/config/console.php';

require_once($yiic);

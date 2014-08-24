<?php

$main = require(dirname(__FILE__) . '/main.php');
unset($main['defaultController']);
unset($main['OnBeginRequest']);
return $main;

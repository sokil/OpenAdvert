<?php

abstract class ChartWidget extends CWidget
{
    public static function loadStatic()
    {
        static $jsapiIncluded = false;
        
        if(!$jsapiIncluded) {
            Yii::app()->getClientScript()->registerScriptFile('https://www.google.com/jsapi');
            
            $config = 'google.load("visualization", "1", {packages:["corechart"]});';
            Yii::app()->getClientScript()->registerScript('h', $config, true);
            
            $jsapiIncluded = true;
        }
    }
    
    public function __set($name, $value)
    {
        $methodName = 'set' . $name;
        if(!method_exists($this, $methodName)) {
            throw new \Exception('Method ' . $name . ' not found');
        }
        
        call_user_func(array($this, $methodName), $value);
    }
    
    public function __get($name)
    {
        $methodName = 'get' . $name;
        if(!method_exists($this, $methodName)) {
            throw new \Exception('Method ' . $name . ' not found');
        }
        
        return call_user_func(array($this, $methodName));
    }
    
    public function run()
    {
        self::loadStatic();
        
    }
}

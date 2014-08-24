<?php

class RestController extends CController
{
    protected $request;
    
    protected $response;
    
    public $layout = false;
    
    public function __construct($id,$module=null)
    {
        parent::__construct($id, $module);
        
        $this->request = Yii::app()->request;
        $this->response = Yii::app()->response;
    }
}
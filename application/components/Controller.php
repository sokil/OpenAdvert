<?php

class Controller extends CController
{
    protected $request;
    
    protected $response;
    
    public $layout = '//layouts/main';
    
    public $breadcrumbs = array();
    
    public function __construct($id,$module=null)
    {
        parent::__construct($id, $module);
        
        $this->request = Yii::app()->request;
        $this->response = Yii::app()->response;
        
        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerPackage('bootstrap');
        Yii::app()->clientScript->registerScriptFile('/js/common.js');
        Yii::app()->clientScript->registerScriptFile('/js/form.js');
        Yii::app()->clientScript->registerScriptFile('/js/modal.js');
    }
    
    public function filters() {
        return array(
            'accessControl',
        );
    }
    
    public function accessRules() {
        return array(
            array('deny',
                'users' => array('?'),
                'deniedCallback' => function(){
                    Yii::app()->getUser()->loginRequired();
                },
            ),
        );
    }
}
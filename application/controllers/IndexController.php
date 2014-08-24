<?php

class IndexController extends Controller
{
    public function accessRules()
    {
        $rules = parent::accessRules();
        array_unshift($rules, array(
            'allow',
            'actions'   => array('index'),
            'users'     => array('*'),
        ));
        return $rules;
    }
    
    public function actionIndex()
    {        
        switch(Yii::app()->user->getRole()) {
            default:
                $this->redirect('/advertisers');
                break;

            case 'advertiser':
                $this->redirect('/stat/advertiser');
                break;
            
            case 'partner':
                $this->redirect('/stat/partner');
                break;
        }
        
    }

}
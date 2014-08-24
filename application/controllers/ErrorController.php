<?php

class ErrorController extends Controller
{    
    public function accessRules() {
        $rules = parent::accessRules();
        array_unshift($rules, array('allow',
            'actions' => array('error'),
            'users' => array('*'),
        ));
        return $rules;
    }
    
    public function actionError()
    {
        $error=Yii::app()->errorHandler->error;
        
        // HTTP Error
        if($error['type'] == 'CHttpException') {
            switch($error['code']) {
                case 404:
                    $this->pageTitle = _('Page not found');
                    $this->render('error404');
                    break;

                case 403:
                    $this->pageTitle = $error['message'];
                    $this->render('error', array('message' => $error['message']));
                    break;
                
                default:
                    $this->pageTitle = _('Error');
                    $this->render('error');
                    break;
            }
        }
        
        // Internal error
        else {
            $this->render('error');
        }
    }
}


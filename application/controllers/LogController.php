<?php

class LogController extends Controller
{

    public function actionIndex()
    {
        $this->pageTitle = _('Log');
        
        $formModel = new LogForm();
        
        $dataProvider = new \Sokil\Mongo\Yii\DataProvider($formModel->getDataSource(), array(
            'attributes' => array('level', 'category', 'logtime'),
            'pagination'    => array('pageSize' => 20)
        ));

        $this->render('index', array(
            'dataProvider' => $dataProvider,
            'formModel' => $formModel,
        ));
    }

    public function actionView($id)
    {
        $logDoc = Yii::app()->mongo->getCollection('log')->getDocument($id);

        $this->render('view', array(
            'logDoc' => $logDoc,
        ));
    }

}


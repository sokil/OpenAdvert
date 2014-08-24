<?php

class DeleteOldVisitorsCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        Yii::app()->mongo
            ->getCollection('visitors')
            ->clearUnused();
    }

}

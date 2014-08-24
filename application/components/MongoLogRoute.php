<?php

class MongoLogRoute extends CLogRoute 
{
    protected function processLogs($logs) 
    {
        $logCollection = \Yii::app()->mongo->getCollection('log');
        
        foreach ($logs as $log) {
            
            $logCollection
                ->createDocument()
                ->setLevel($log[1])
                ->setCategory($log[2])
                ->setTime($log[3])
                ->setMessage($log[0])
                ->save();
        }
    }

}

<?php

class AddApiKeyCommand extends CConsoleCommand
{
    public function actionIndex($name)
    {
        if(!$name) {
            echo 'Application name not specified' . PHP_EOL;
            return;
        }
        
        // check if application name already exists
        $exists = (bool) Yii::app()->mongo
            ->getCollection('apikeys')
            ->find()
            ->byName($name)
            ->count();
        
        if($exists) {
            echo 'Application with specified name already registered' . PHP_EOL;
            return;
        }
        
        // add new key
        $key = Yii::app()->mongo
            ->getCollection('apikeys')
            ->createDocument([
                'name' => $name,
            ])
            ->save();
        
        echo 'Application name: ' . $key->name . PHP_EOL;
        echo 'Application ID: ' . $key->getId() . PHP_EOL;
        echo 'Application key: ' . $key->key . PHP_EOL;
    }
    
    public function getHelp()
    {
        
    }
}
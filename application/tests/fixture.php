<?php

$database = \Yii::app()->mongo;

$collections = glob(__DIR__ . '/fixture/*.php');
foreach ($collections as $collectionPath) {
    $collectionName = pathinfo($collectionPath, PATHINFO_FILENAME);
    $collection = $database->getCollection($collectionName)->getMongoCollection();
    $collection->drop();
    $docs = require($collectionPath);
    foreach ($docs as $doc) {
        $collection->insert($doc);
    }
}

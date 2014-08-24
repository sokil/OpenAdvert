<?php

namespace MongoAdvertDb\Track;

class Event extends \Sokil\Mongo\Collection {

    protected $_queryExpressionClass = '\MongoAdvertDb\Track\Event\QueryExpression';

    public function getDocumentClassName(array $documentData = null) {
        return '\MongoAdvertDb\Track\Event\Record';
    }

}
<?php

namespace MongoAdvertDb\Track;

class Click extends \Sokil\Mongo\Collection {

    protected $_queryExpressionClass = '\MongoAdvertDb\Track\Click\QueryExpression';

    public function getDocumentClassName(array $documentData = null) {
        return '\MongoAdvertDb\Track\Click\Record';
    }

}
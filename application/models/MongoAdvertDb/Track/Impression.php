<?php

namespace MongoAdvertDb\Track;

class Impression extends \Sokil\Mongo\Collection {

    protected $_queryExpressionClass = '\MongoAdvertDb\Track\Impression\QueryExpression';

    public function getDocumentClassName(array $documentData = null) {
        return '\MongoAdvertDb\Track\Impression\Record';
    }

}

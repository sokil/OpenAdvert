<?php

namespace MongoAdvertDb;

class Log extends \Sokil\Mongo\Collection
{

    protected $_queryExpressionClass = '\MongoAdvertDb\Log\QueryExpression';

    public function getDocumentClassName(array $documentData = null)
    {
        return '\MongoAdvertDb\Log\Record';
    }

}

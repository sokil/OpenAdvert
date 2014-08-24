<?php 

namespace MongoAdvertDb;

class Zones extends \Sokil\Mongo\Collection
{
    protected $_queryBuliderClass = '\MongoAdvertDb\Zones\QueryBuilder';
    
    protected $_queryExpressionClass = '\MongoAdvertDb\Zones\QueryExpression';
    
    public function getDocumentClassName(array $documentData = null) {
        return '\MongoAdvertDb\Zones\Zone';
    }
}
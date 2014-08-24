<?php 

namespace MongoAdvertDb;

class Users extends \Sokil\Mongo\Collection
{
    protected $_queryExpressionClass = '\MongoAdvertDb\Users\QueryExpression';
    
    public function getDocumentClassName(array $documentData = null) {
        return '\MongoAdvertDb\Users\User';
    }
}
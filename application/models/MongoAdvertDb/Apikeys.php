<?php

namespace MongoAdvertDb;

class Apikeys extends \Sokil\Mongo\Collection
{
    protected $_queryBuliderClass = '\MongoAdvertDb\Apikeys\QueryBuilder';
    
    public function getDocumentClassName(array $documentData = null)
    {
        return '\MongoAdvertDb\Apikeys\Key';
    }
}
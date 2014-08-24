<?php 

namespace MongoAdvertDb;

class Advertisers extends \Sokil\Mongo\Collection
{
    protected $_queryExpressionClass = '\MongoAdvertDb\Advertisers\QueryExpression';
    
    public function getDocumentClassName(array $documentData = null) {
        return '\MongoAdvertDb\Advertisers\Advertiser';
    }
    
    public function getActiveAdvertisersList()
    {
        return $this->find()->notDeleted()->sort(array('name' => 1));
    }
}
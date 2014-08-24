<?php 

namespace MongoAdvertDb;

class BannerExchangeList extends \Sokil\Mongo\Collection
{
    protected $_queryExpressionClass = '\MongoAdvertDb\BannerExchangeList\QueryExpression';
    
    public function getDocumentClassName(array $documentData = null) {
        return '\MongoAdvertDb\BannerExchangeList\Banner';
    }
}
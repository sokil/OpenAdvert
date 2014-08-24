<?php
namespace MongoAdvertDb;

class Partners extends \Sokil\Mongo\Collection
{
    protected $_queryExpressionClass = '\MongoAdvertDb\Partners\QueryExpression';

    public function getDocumentClassName(array $documentData = null) {
        return '\MongoAdvertDb\Partners\Partner';
    }

    public function getActivePartnersList()
    {
        return $this->find()->notDeleted()->sort(array('name' => 1));
    }
}
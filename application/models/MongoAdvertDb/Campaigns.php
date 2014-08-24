<?php

namespace MongoAdvertDb;

class Campaigns extends \Sokil\Mongo\Collection
{

    protected $_queryExpressionClass = '\MongoAdvertDb\Campaigns\QueryExpression';

    public function getDocumentClassName(array $documentData = null)
    {
        return '\MongoAdvertDb\Campaigns\Campaign';
    }

    public function deactivateByCampaigns(array $campaigns)
    {
        $expression = $this->expression()->whereIn('_id', array_map(function($campaign) {
            return $campaign->getId();
        }, $campaigns));

        $this->updateMultiple(
            $expression,
            $this->operator()->set('deactivated', true)
        );

        \Yii::app()->mongo->getCollection('banners')->deactivateByCampaigns($campaigns);
    }

}

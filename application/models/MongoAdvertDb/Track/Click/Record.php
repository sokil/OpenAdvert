<?php

namespace MongoAdvertDb\Track\Click;

class Record extends \MongoAdvertDb\Track\Base\Record
{
    public function beforeConstruct() {
        parent::beforeConstruct();
        $this->onBeforeInsert(function() {            
            // increment event counter in campaign
            \Yii::app()->mongo->getCollection('campaigns')
                ->getDocument($this->getBannerCampaignId())
                ->hitClicks($this->getBanner())
                ->save();
        });
    }
}
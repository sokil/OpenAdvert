<?php

namespace MongoAdvertDb\Track\Base;

use MongoAdvertDb\Banners\Banner;
use MongoAdvertDb\Visitors\Visitor;

abstract class Record extends \Sokil\Mongo\Document
{
    protected $_data = array(
        'visitor'   => null,
        'banner'    => array(
            'id'            => null,
            'campaign'      => null,
            'advertiser'    => null,
        ),
        'date'      => null,
        'userAgent' => null,
        /**
         * Optional:
         * 'partner' => null,
         */
    );
    
    public function beforeConstruct() {
        
        $this->onBeforeInsert(function() {
            // define date
            $this->set('date', new \MongoDate);
            
            $visitor = \Yii::app()->mongo->getCollection('visitors')->getCurrent();
            
            // define visitor
            if(!$this->get('visitor')) {
                $this->setVisitor($visitor);
            }
        });
    }
    
    public function setBanner(Banner $banner)
    {
        $advertiserId = \Yii::app()->mongo->getCollection('campaigns')
            ->getDocument($banner->getCampaignId())
            ->getAdvertiserId();
        
        $this->set('banner.id', $banner->getId());
        $this->set('banner.campaign', $banner->getCampaignId());
        $this->set('banner.advertiser', $advertiserId);
        
        return $this;
    }
    
    /**
     * 
     * @return \MongoAdvertDb\Banners\Banner
     */
    public function getBanner()
    {
        return \Yii::app()
            ->mongo
            ->getCollection('banners')
            ->getDocument($this->getBannerId());
    }
    
    public function getBannerId()
    {
        return $this->get('banner.id');
    }
    
    public function getBannerCampaignId()
    {
        return $this->get('banner.campaign');
    }
    
    public function setVisitor(Visitor $visitor)
    {
        $this->set('visitor', $visitor->getId());
        $this->set('userAgent', $visitor->getUserAgent());

        // define visitor's agent
        if($visitor->getPartnerId()) {
            $this->set('partner', $visitor->getPartnerId());
        }
        
        return $this;
    }
}
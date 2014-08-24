<?php

namespace MongoAdvertDb\Campaigns;

use \MongoAdvertDb\Advertisers\Advertiser;

class QueryExpression extends \Sokil\Mongo\Expression
{
    public function byAdvertiser(Advertiser $advertiser)
    {
        $this->where('advertiser', $advertiser->getId());
        return $this;
    }
    
    public function byAdvertisers(array $advertisers)
    {
        $this->where('advertiser', array('$in' => array_map(function($advertiser) {
            return $advertiser->getId();
        }, $advertisers)));
        return $this;
    }
    
    public function active()
    {
        $this->where('status', \MongoAdvertDb\Campaigns\Campaign::STATUS_ACTIVE);
        return $this;
    }
    
    public function notDeleted()
    {
        $this->where('status', array('$ne' => \MongoAdvertDb\Campaigns\Campaign::STATUS_DELETED));
        return $this;
    }
    
    public function byNameLike($name) 
    {
        $this->where('name', array('$regex' => $name, '$options' => 'i'));
        return $this;
    }
    
    public function notDeactivated()
    {
        $this->whereNotExists('deactivated');
        return $this;
    }
    
    public function endedByNow()
    {
        $this->where('dateTo', array('$lt' => new \MongoDate()));
        return $this;
    }

}

<?php

namespace MongoAdvertDb\BannerExchangeStat;

use \MongoAdvertDb\Advertisers\Advertiser;
use \MongoAdvertDb\BannerExchangeList\Banner;

class Record extends \Sokil\Mongo\Document
{
    protected $_data = array(
        'player'    => null,
        'owner'    => null,
        'banner'    => null,
        'datetime'  => null,
        'event'     => null,
    );
    
    public function setPlayer(Advertiser $advertiser)
    {
        return $this->set('player', $advertiser->getId());
    }
    
    public function getPlayerId()
    {
        return $this->get('player');
    }
    
    public function setBannerOwner(Advertiser $advertiser)
    {
        return $this->set('owner', $advertiser->getId());
    }
    
    public function setBanner(Banner $banner)
    {
        $this->set('banner', $banner->getId());
        
        return $this;
    }
    
    public function getBannerId()
    {
        return $this->get('banner');
    }
    
    public function setDatetime($datetime)
    {
        if(!is_numeric($datetime)) {
            $datetime = strtotime($datetime);
            if(!$datetime) {
                throw new \Exception('Date format is wrong');
            }
        }
        
        $this->set('datetime', new \MongoDate($datetime));
        return $this;
    }
    
    public function getDatetime()
    {
        return (int) $this->get('datetime')->sec;
    }
    
    public function setEvent($event)
    {
        $this->set('event', $event);
        return $this;
    }
    
    public function getEvent()
    {
        return $this->get('event');
    }
}
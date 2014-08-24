<?php

namespace MongoAdvertDb\BannerExchangeList;

use \MongoAdvertDb\Advertisers\Advertiser;

class Banner extends \Sokil\Mongo\Document
{
    protected $_data = array(
        'name'          => null,
        'advertiser'    => null,    // link to advertiser document
        'video'         => array(
            'url'   => null,
            'mime'  => null,
            'height'    => null,
            'width'     => null,
            'delivery'  => 'progressive',
            'duration'  => null,
        ),
        'url'           => null,
    );
    
    public function setName($name)
    {
        return $this->set('name', $name);
    }
    
    public function getName()
    {
        return $this->get('name');
    }
    
    public function setAdvertiser(Advertiser $advertiser)
    {
        return $this->set('advertiser', $advertiser->getId());
    }
    
    public function getAdvertiserId()
    {
        return $this->get('advertiser');
    }
    
    public function setImpression($url)
    {
        return $this->set('impression', $url);
    }
    
    public function getImpression()
    {
        return $this->get('impression');
    }
    
    public function setVideoUrl($url)
    {
        return $this->set('video.url', $url);
    }
    
    public function getVideoUrl()
    {
        return $this->get('video.url');
    }
    
    public function setVideoDuration($duration)
    {
        return $this->set('video.duration', $duration);
    }
    
    public function getVideoDuration()
    {
        return $this->get('video.duration');
    }
    
    public function setProgressiveVideoDelivery()
    {
        return $this->set('video.delivery', \Sokil\Vast\Ad\InLine\Creative\Base\MediaFile::DELIVERY_PROGRESSIVE);
    }
    
    public function setStreamingVideoDelivery()
    {
        return $this->set('video.delivery', \Sokil\Vast\Ad\InLine\Creative\Base\MediaFile::DELIVERY_STREAMING);
    }
    
    public function getVideoDelivery()
    {
        return $this->get('video.delivery');
    }
    
    public function setVideoType($mime)
    {
        return $this->set('video.mime', $mime);
    }
    
    public function getVideoType()
    {
        return $this->get('video.mime');
    }
    
    public function setVideoSize($width, $height)
    {
        $this->set('video.height', $height);
        $this->set('video.width', $width);
        
        return $this;
    }
    
    public function getVideoHeight()
    {
        return $this->get('video.height');
    }
    
    public function getVideoWidth()
    {
        return $this->get('video.width');
    }
    
    public function setUrl($url)
    {
        return $this->set('url', $url);
    }
    
    public function getUrl()
    {
        return $this->get('url');
    }
    
    public function getTrackUrl($event, Advertiser $referal)
    {
        return \Yii::app()->request->getHostInfo() . '/bc/track?' . http_build_query(array(
            'e'     => $event,
            'b'     => (string) $this->getId(),
            'ref'   => (string) $referal->getId()
        ));
    }
}
<?php

namespace MongoAdvertDb\Zones\Zone\VASTRenderer;

use \MongoAdvertDb\Banners\Banner\VideoBanner;

class VideoBannerVASTRenderer extends \MongoAdvertDb\Zones\Zone\VASTRenderer
{
    /**
     *
     * @var \MongoAdvertDb\Banners\Banner\VideoBanner
     */
    private $_banner;
    
    public function setBanner(VideoBanner $banner)
    {
        $this->_banner = $banner;
        return $this;
    }
    
    public function render()
    {
        if(!$this->_banner) {
            return $this->_vastDocument;
        }
        
        // common info
        $this->_ad
            ->setImpression($this->_banner->getTrackImpressionUrl());
        
        // ad linear creative
        $creative = $this->_ad->createLinearCreative()
            ->setDuration($this->_banner->getDuration())
            ->setVideoClipsClickThrough($this->_banner->geClickThroughUrl());
        if ($this->_banner->getSkipOffset()) {
            $creative->skipAfter($this->_banner->getSkipOffset());
        }

        // add media files
        foreach ($this->_banner->getMediaFiles() as $mediaFile) {
            $creative->createMediaFile()
                ->setType($mediaFile->getType())
                ->setDelivery($mediaFile->getDelivery())
                ->setWidth($mediaFile->getWidth())
                ->setHeight($mediaFile->getHeight())
                ->setUrl($mediaFile->getCanonicalUrl());
            
        }
        
        // internal event tracking urls
        foreach(\Sokil\Vast\Ad\InLine\Creative\Linear::getEventList() as $event) {
            $creative->addTrackingEvent($event, $this->_banner->getTrackEventUrl($event));
        }

        foreach($this->_banner->getEvents() as $event => $urls) {
            foreach($urls as $url) {
                $creative->addTrackingEvent($event, $url);
            }
        }
        
        return $this->_vastDocument;
    }
}
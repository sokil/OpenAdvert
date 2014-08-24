<?php

namespace MongoAdvertDb\Banners\Banner\VideoBanner;

class MediaFile extends \Sokil\Mongo\Structure
{
    protected $_data = array(
        'id'        => null,
        'delivery'  => self::DELIVERY_PROGRESSIVE,
        'type'      => null,
        'height'    => null,
        'width'     => null,
        'url'       => null,
        'duration'  => null,
    );
    
    const DELIVERY_PROGRESSIVE = 'progressive';
    const DELIVERY_STREAMING = 'streaming';
    
    private static $_uploader;
    
    public static function create()
    {
        return new self();        
    }
    
    /**
     * 
     * @return \MongoAdvertDb\Banners\Uploader\VideoBannerUploader
     */
    public static function getUploader()
    {
        if(!self::$_uploader) {
            self::$_uploader = new \MongoAdvertDb\Banners\Uploader\VideoBannerUploader;
        }
        
        return self::$_uploader;
    }
    
    public function setId($id)
    {
        if(!($id instanceof \MongoId)) {
            $id = new \MongoId($id);
        }
        
        $this->set('id', $id);
        return $this;
    }
    
    public function getId()
    {
        return $this->get('id');
    }
    
    public function setProgressiveDelivery()
    {
        $this->set('delivery', self::DELIVERY_PROGRESSIVE);
        return $this;
    }
    
    public function setStreamingDelivery()
    {
        $this->set('delivery', self::DELIVERY_STREAMING);
        return $this;
    }
    
    public function setDelivery($delivery)
    {
        if(!in_array($delivery, array(self::DELIVERY_PROGRESSIVE, self::DELIVERY_STREAMING))) {
            throw new \Exception('Wrong delivery specified');
        }
        
        $this->set('delivery', $delivery);
        return $this;
    }
    
    public function getDelivery()
    {
        return $this->get('delivery');
    }
    
    public function getType()
    {
        return $this->get('type');
    }
    
    public function getWidth()
    {
        return (int) $this->get('width');
    }
    
    public function getHeight()
    {
        return (int) $this->get('height');
    }
    
    public function getSize()
    {
        return $this->getHeight() . 'x' . $this->getWidth();
    }
    
    public function getRealpath()
    {
        if($this->isLocallyStored()) {
            return PUBLIC_PATH . $this->getUrl();
        }
        else {
            return $this->getUrl();
        }
    }
    
    public function getDuration()
    {
        return (int) $this->get('duration');
    }
    
    public function isLocallyStored()
    {
        return '/' === substr($this->getUrl(), 0, 1);
    }
    
    private function _codec2mime($codec)
    {
        switch($codec) {
            default:
            case 'h264' : return 'video/mp4';
            case 'flv'  : return 'video-x-flv';
        }
    }
    
    /**
     * 
     * @param type $url
     * @return \MongoAdvertDb\Banners\Banner\VideoBanner\MediaFile
     */
    public function setUrl($url)
    {
        $oldUrl = $this->getUrl();
        if($oldUrl === $url) {
            return $this;
        }
        
        $this->set('url', $url);
        
        $info = \FFMpeg\FFProbe::create(array(
            'ffprobe.binaries'  => \Yii::app()->params['ffprobe_path']
        ))
            ->streams($this->getRealpath())
            ->videos()
            ->first();
        
        $dimensions = $info->getDimensions();
        
        $this
            ->set('height'      , $dimensions->getHeight())
            ->set('width'       , $dimensions->getWidth())
            ->set('type'        , $this->_codec2mime($info->get('codec_name')))
            ->set('duration'    , $info->get('duration'));
            
        
        return $this;
    }
    
    public function getUrl()
    {
        return $this->get('url');
    }
    
    public function getCanonicalUrl()
    {
        if(!$this->isLocallyStored()) {
            return $this->getUrl();
        }
        
        return 'http://' . $_SERVER['HTTP_HOST'] . $this->getUrl();
    }
    
    public function upload()
    {
        $status = self::getUploader()->setMediaFile($this)->upload();
        $this->setUrl(str_replace(PUBLIC_PATH, '', $status['path']));
        return $this;
    }
}

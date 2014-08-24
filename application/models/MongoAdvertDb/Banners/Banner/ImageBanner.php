<?php

namespace MongoAdvertDb\Banners\Banner;

class ImageBanner extends \MongoAdvertDb\Banners\Banner
{
    private static $_uploader;
    
    /**
     * 
     * @return \MongoAdvertDb\Banners\Uploader\ImageBannerUploader
     */
    public static function getUploader()
    {
        if(!self::$_uploader) {
            self::$_uploader = new \MongoAdvertDb\Banners\Uploader\ImageBannerUploader;
        }
        
        return self::$_uploader;
    }
    
    public function beforeConstruct() {
        
        parent::beforeConstruct();
        
        $this->_data = array_merge($this->_data, array(
            'imageUrl' => null,
        ));
    }
    
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('imageUrl', 'required'),
        ));
    }
    
    public function getImageUrl()
    {
        return $this->get('imageUrl');
    }
    
    public function isImageLocallyStored()
    {
        return '/' === substr($this->getImageUrl(), 0, 1);
    }
    
    public function getCanonicalImageUrl()
    {
        $url = $this->getImageUrl();
        
        if($this->isImageLocallyStored()) {
            return 'http://' .  $_SERVER['HTTP_HOST'] . $url;
        }
        
        // url from remote storage
        return $url;
    }
    
    public function setImageUrl($imageUrl)
    {
        // if image url previously assigned and is locally stored
        $prevImageUrl = $this->getImageUrl();
        
        if($prevImageUrl && $prevImageUrl !== $imageUrl && $this->isImageLocallyStored()) {
            unlink(PUBLIC_PATH . $prevImageUrl);
        }
        
        $this->set('imageUrl', $imageUrl);
        return $this;
    }
    
    public function upload()
    {
        $status = self::getUploader()->setBanner($this)->upload();
        
        // define urls
        $this->setImageUrl(str_replace(PUBLIC_PATH, '', $status['path']));
    }
}
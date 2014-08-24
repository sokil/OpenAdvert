<?php

namespace MongoAdvertDb\Banners\Uploader;

use \MongoAdvertDb\Banners\Banner\ImageBanner;

class ImageBannerUploader extends \MongoAdvertDb\Banners\Uploader
{
    protected $_supportedFormats = array('jpg', 'jpeg', 'gif', 'png');
    
    /**
     * @param \MongoAdvertDb\Banners\Banner\ImageBanner $banner
     * @return \MongoAdvertDb\Banners\Uploader\ImageBannerUploader
     */
    public function setBanner(ImageBanner $banner)
    {
        $id = $banner->getId();
        if(!$id) {
            throw new \Exception('Banner must be saved before uploading file');
        }
        
        $this->setId($id);
        return $this;
    }
}
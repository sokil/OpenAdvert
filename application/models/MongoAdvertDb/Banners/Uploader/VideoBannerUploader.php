<?php

namespace MongoAdvertDb\Banners\Uploader;

use MongoAdvertDb\Banners\Banner\VideoBanner\MediaFile;

class VideoBannerUploader extends \MongoAdvertDb\Banners\Uploader
{
    protected $_supportedFormats = array('flv', 'mp4');
    
    /**
     * @param \MongoAdvertDb\Banners\Banner\VideoBanner $banner
     * @return \MongoAdvertDb\Banners\Uploader\VideoBannerUploader
     */
    public function setMediaFile(MediaFile $mediaFile)
    {
        $this->setId($mediaFile->getId());
        return $this;
    }
}
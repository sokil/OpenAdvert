<?php

namespace MongoAdvertDb\Banners;

abstract class Uploader
{
    protected $_supportedFormats = array();
    
    private $_id;
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function setId($id)
    {
        $this->_id = (string) $id;
        return $this;
    }
    
    public function upload()
    {
        // dir from web root
        $webRootDir = implode('/', array(
            \Yii::app()->params['bannerStorageDir'],
            substr($this->_id, -2, 2),
            substr($this->_id, -4, 2),
            substr($this->_id, -6, 2)
        ));
        
        // dir from system root
        $dir = PUBLIC_PATH . $webRootDir;
        
        // get previous sypport fromat
        $supportedFormats = \Yii::app()->uploader->getSupportedFormats();
        
        // upload
        return \Yii::app()->uploader
            // configure local supported formats
            ->setSupportedFormats($this->_supportedFormats)
            // upload
            ->upload($dir, $this->_id)
            // restore previous supported formats config
            ->setSupportedFormats($supportedFormats)
            // get status
            ->getLastUploadStatus();
    }
}
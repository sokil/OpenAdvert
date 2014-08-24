<?php

namespace MongoAdvertDb\Banners\Banner;

use MongoAdvertDb\Banners\Banner\VideoBanner\MediaFile;

class VideoBanner extends \MongoAdvertDb\Banners\Banner
{    
    public function beforeConstruct() {
        
        parent::beforeConstruct();
        
        $this->_data = array_merge($this->_data, array(
            'mediaFiles'    => array(),
            'duration'      => null,
        ));
    }
    
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('duration', 'numerical'),
        ));
    }
    
    public function setDuration($duration)
    {
        $this->set('duration', $duration);
        return $this;
    }
    
    public function getDuration()
    {
        return $this->get('duration');
    }
    
    public function setSkipOffset($skipoffset)
    {
        $this->set('skipoffset', $skipoffset);
        return $this;
    }
    
    public function getSkipOffset()
    {
        return $this->get('skipoffset');
    }
    
    /**
     * 
     * @param type $id
     * @return \MongoAdvertDb\Banners\Banner\VideoBanner\MediaFile
     */
    public function getMediaFile($id)
    {
        foreach($this->getMediaFiles() as $mediaFile) {
            if((string) $mediaFile->getId() === (string) $id) {
                return $mediaFile;
            }
        }
        
        return null;
    }
    
    public function addMediaFile(MediaFile $mediaFile)
    {
        $this->push('mediaFiles', $mediaFile);
        return $this;
    }
    
    public function deleteMediaFile($id)
    {
        // get media file
        $mediaFile = $this->getMediaFile($id);
        
        // delete file physically
        if($mediaFile->isLocallyStored()) {
            unlink(PUBLIC_PATH . $mediaFile->getUrl());
        }
        
        // remove from db
        return $this->pull('mediaFiles', array(
            'id' => new \MongoId($id),
        ));
    }
    
    public function setMediaFiles(array $mediaFiles)
    {
        $this->set('mediaFiles', array_map(function($mediaFile) {
            return $mediaFile->toArray();
        }, $mediaFiles));
    }
    
    public function getMediaFiles()
    {
        return $this->getObjectList('mediaFiles', '\MongoAdvertDb\Banners\Banner\VideoBanner\MediaFile');
    }
    
    /**
     * 
     * @param array $events {eventName => {...urls...}}
     */
    public function setEvents(array $events)
    {
        $this->set('events', $events);
    }
    
    public function clearEvents()
    {
        $this->set('events', array());
    }
    
    public function getEvents()
    {
        return $this->get('events');
    }
}
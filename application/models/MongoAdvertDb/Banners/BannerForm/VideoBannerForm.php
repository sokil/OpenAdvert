<?php

namespace MongoAdvertDb\Banners\BannerForm;

/**
 * @method \MongoAdvertDb\Banners\Banner\VideoBanner getBanner() get banner, relative to this form
 */
class VideoBannerForm extends \MongoAdvertDb\Banners\BannerForm
{
    public $skipoffset;
    public $events;
    
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('skipoffset', 'numerical'),
            array('events', 'type', 'type' => 'array'),
        ));
    }
    
    protected function applyAttributes()
    {
        $banner = $this->getBanner();
        
        $banner->setSkipOffset($this->skipoffset);
        
        /**
         * Events
         */
        $events = $this->getAttributes()['events'];
        if($events) {
            $banner->setEvents($events);
        }
        else {
            $banner->clearEvents();
        }
    }
}
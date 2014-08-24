<?php

namespace MongoAdvertDb\Track\Event;

use Sokil\Vast\Ad\InLine\Creative\Linear as LinearCreative;

class Record extends \MongoAdvertDb\Track\Base\Record
{
    public function beforeConstruct() {
        parent::beforeConstruct();
        $this->_data = array_merge($this->_data, array(
            'event' => null,
        ));
    }
    
    public function setEvent($event)
    {
        if(!in_array($event, LinearCreative::getEventList())) {
            throw new \Exception('Wrong event specified');
        }
        
        $this->set('event', $event);
        return $this;
    }
}
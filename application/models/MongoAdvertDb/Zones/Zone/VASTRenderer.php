<?php

namespace MongoAdvertDb\Zones\Zone;

use \MongoAdvertDb\Zones\Zone;

abstract class VASTRenderer
{
    /**
     *
     * @var \MongoAdvertDb\Zones\Zone
     */
    protected $_zone;
    
    /**
     *
     * @var \Sokil\Vast\Document
     */
    protected $_vastDocument;
    
    /**
     *
     * @var \Sokil\Vast\Ad|\Sokil\Vast\Ad\InLine|\Sokil\Vast\Wrapper
     */
    protected $_ad;
    
    public function __construct(Zone $zone) {
        
        $this->_zone = $zone;
        
        $this->_vastDocument = \Sokil\Vast\Document::create();
        
        $this->_ad = $this->_vastDocument->createInLineAdSection()
            ->setId($this->_zone->getId())
            ->setAdSystem(\Yii::app()->name)
            ->setAdTitle($this->_zone->getName());
    }
    
    /**
     * @return \Sokil\Vast\Document
     */
    abstract public function render();
}
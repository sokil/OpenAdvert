<?php

namespace MongoAdvertDb\Visitors;

use MongoAdvertDb\Partners\Partner;

class Visitor extends \Sokil\Mongo\Document
{
    protected $_date = array(
        'created'       => null,
        'lastActive'    => null,
        'userAgent'     => null,
    );
    
    public function beforeConstruct() {
        /**
         * After Construct hook
         */
        $this->onAfterConstruct(function() {
            // update last active
            $this->set('lastActive', new \MongoDate)->save();
        });
        
        /**
         * Before Save hook
         */
        $this->onBeforeInsert(function() {
        
            // user agent
            $browser = get_browser($_SERVER['HTTP_USER_AGENT']);
            $this->set('userAgent.plain', $_SERVER['HTTP_USER_AGENT']);
            $this->set('userAgent.platform', $browser->platform);

            // created
            $this->set('created', new \MongoDate);
            
            return $this;
        });
    }
    
    public function getUserAgent()
    {
        return $this->get('userAgent');
    }
    
    public function markLastActive()
    {
        $this
            ->set('lastActive', new \MongoDate)
            ->save();
        
        return $this;
    }
    
    public function setPartner(Partner $partner)
    {
        $this->set('partner', $partner->getId());
        return $this;
    }
    
    public function getPartnerId()
    {
        return $this->get('partner');
    }
}
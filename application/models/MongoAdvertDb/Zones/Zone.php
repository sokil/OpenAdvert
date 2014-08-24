<?php

namespace MongoAdvertDb\Zones;

class Zone extends \Sokil\Mongo\Document
{
    const STATUS_ACTIVE     = 'ACTIVE';
    const STATUS_SUSPENDED  = 'SUSPENDED';
    const STATUS_DELETED    = 'DELETED';
    
    protected $_data = array(
        'name'              => null,
        'type'              => null,
        'status'            => self::STATUS_ACTIVE,
    );
    
    public function rules()
    {
        return array(
            array('name,status', 'required', 'message' => _('This field required')),
            array('status', 'in', 'range' => array(self::STATUS_ACTIVE, self::STATUS_SUSPENDED, self::STATUS_DELETED), 'message' => _('Wrong value specified')),
        );
    }
    
    public function setName($name)
    {
        return $this->set('name', $name);
    }
    
    public function getName()
    {
        return $this->get('name');
    }
    
    public function setType($type)
    {
        return $this->set('type', $type);
    }
    
    public function getType()
    {
        return $this->get('type');
    }
    
    public function getStatus()
    {
        return $this->get('status');
    }
    
    public function setActive()
    {
        $this->set('status', self::STATUS_ACTIVE);
        return $this;
    }
    
    public function isActive()
    {
        return self::STATUS_ACTIVE === $this->get('status');
    }
    
    public function setSuspended()
    {
        $this->set('status', self::STATUS_SUSPENDED);
        return $this;
    }
    
    public function isSuspended()
    {
        return self::STATUS_SUSPENDED === $this->get('status');
    }
    
    public function setDeleted()
    {
        $this->set('status', self::STATUS_DELETED);
        return $this;
    }
    
    /**
     * 
     * @return \MongoAdvertDb\Zones\Zone\VASTRenderer
     */
    public function getVASTRenderer()
    {
        $className = '\\MongoAdvertDb\\Zones\\Zone\\VASTRenderer\\' . ucfirst($this->getType()) . 'BannerVASTRenderer';
        if(!class_exists($className)) {
            throw new \Exception('VAST renderer not found');
        }
        
        return new $className($this);
    }
}
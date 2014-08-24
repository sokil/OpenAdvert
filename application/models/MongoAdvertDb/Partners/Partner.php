<?php
namespace MongoAdvertDb\Partners;

class Partner extends \Sokil\Mongo\Document
{
    const STATUS_ACTIVE     = 'ACTIVE';
    const STATUS_DELETED    = 'DELETED';

    protected $_data = array(
        'ref'       => null,
        'name'      => null,
        'status'    => self::STATUS_ACTIVE,
    );

    public function __toString()
    {
        return $this->getName();
    }
    
    public function rules()
    {
        return array(
            array('ref,name,status', 'required', 'message' => _('This field required')),
            array('status', 'in', 'range' => array(self::STATUS_ACTIVE, self::STATUS_DELETED)),
        );
    }

    public function setRef($name)
    {
        return $this->set('ref', $name);
    }

    /**
     * @deprecated use self::getPartnerId()
     * @return string partner id
     */
    public function getRef()
    {
        return $this->getPartnerId();
    }
    
    public function getPartnerId()
    {
        return $this->get('ref');
    }

    public function setName($name)
    {
        return $this->set('name', $name);
    }

    public function getName()
    {
        return $this->get('name');
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
        return self::STATUS_ACTIVE === $this->getStatus();
    }

    public function setDeleted()
    {
        $this->set('status', self::STATUS_DELETED);
        return $this;
    }

    public function isDeleted()
    {
        return self::STATUS_DELETED === $this->getStatus();
    }

}
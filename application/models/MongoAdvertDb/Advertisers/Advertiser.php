<?php

namespace MongoAdvertDb\Advertisers;

class Advertiser extends \Sokil\Mongo\Document
{
    const STATUS_ACTIVE     = 'ACTIVE';
    const STATUS_DELETED    = 'DELETED';
    
    protected $_data = array(
        'name'      => null,
        'phone'     => null,
        'address'   => null,
        'email'     => null,
        'bc_credit' => 1000,
        'status'    => self::STATUS_ACTIVE,
    );
    
    public function __toString()
    {
        return $this->getName();
    }
    
    public function beforeConstruct()
    {
        $this->onBeforeSave(function() {

                if ($this->isModified('status') && $this->isDeleted()) {
                    $campaigns = \Yii::app()->mongo->getCollection('campaigns')
                        ->find()
                        ->byAdvertiser($this);
                    foreach ($campaigns as $campaign) {
                        $campaign->setDeleted()->save();
                    }
                    
                    $users = \Yii::app()->mongo->getCollection('users')
                        ->find()
                        ->byAdvertiser($this);
                    foreach ($users as $user) {
                        $user->setDeleted()->save();
                    }
                }
                
            });
    }

    public function rules()
    {
        return array(
            array('name,status', 'required', 'message' => _('This field required')),
            array('status', 'in', 'range' => array(self::STATUS_ACTIVE, self::STATUS_DELETED)),
            array('bc_credit', 'numeric', 'message' => _('Field must be numeric')),
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
    
    public function setPhone($phone)
    {
        return $this->set('phone', $phone);
    }
    
    public function getPhone()
    {
        return $this->get('phone');
    }
    
    public function setAddress($address)
    {
        return $this->set('address', $address);
    }
    
    public function getAddress()
    {
        return $this->get('address');
    }
    
    public function setEmail($email)
    {
        return $this->set('email', $email);
    }
    
    public function getEmail()
    {
        return $this->get('email');
    }
    
    public function increaseCredit()
    {
        return $this->increment('bc_credit');
    }
    
    public function decreaseCredit()
    {
        return $this->decrement('bc_credit');
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
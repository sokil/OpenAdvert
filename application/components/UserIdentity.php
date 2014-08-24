<?php

class UserIdentity extends CUserIdentity
{
    /**
     *
     * @var \Sokil\Mongo\Document
     */
    private $_user;
    
    public function authenticate()
    {        
        $this->_user = Yii::app()->mongo->getCollection('users')
            ->find()
            ->active()
            ->byEmail($this->username)
            ->findOne();
        
        if(!$this->_user) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }
        else {
            $this->errorCode = $this->_user->isPasswordEquals($this->password)
                ? self::ERROR_NONE
                : self::ERROR_PASSWORD_INVALID;
        }

        return $this->errorCode === self::ERROR_NONE;
    }
    
    public function getId()
    {
        return (string) $this->_user->getId();
    }
    
    public function getName()
    {
        return $this->_user->getName();
    }
}
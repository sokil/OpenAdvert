<?php

class User extends CWebUser
{

    /**
     * 
     * @return \MongoAdvertDb\Users\User
     */
    public function getProfile()
    {
        return Yii::app()->session['user'] = Yii::app()->mongo
            ->getCollection('users')
            ->getDocument($this->getId());
    }

    public function getRole()
    {
        $profile = $this->getProfile();
        if(!$profile) {
            return null;
        }
        
        return $profile->getRole();
    }

    public function hasRole($role)
    {
        return $role === $this->getRole();
    }

}
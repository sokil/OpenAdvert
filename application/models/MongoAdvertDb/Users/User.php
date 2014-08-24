<?php

namespace MongoAdvertDb\Users;

use \MongoAdvertDb\Advertisers\Advertiser;
use \MongoAdvertDb\Partners\Partner;

class User extends \Sokil\Mongo\Document
{

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_DELETED = 'DELETED';

    const ROLE_MANAGER      = 'manager';
    const ROLE_ADVERTISER   = 'advertiser';
    const ROLE_PARTNER      = 'partner';

    private $_changedPassword;
    protected $_data = array(
        'email'    => null,
        'password' => null,
        'salt'     => null,
        'role'     => null,
        'name'     => null,
        'phone'    => null,
        'status'   => self::STATUS_ACTIVE,
    );

    public function beforeConstruct()
    {
        // send main about update
        $this->onAfterUpdate(function() {
            if ($this->isDeleted()) {
                return;
            }

            $mailer = \Yii::app()->mailer;
            $mailer->addAddress($this->getEmail(), $this->getName());
            $mailer->setSubject(sprintf(_('Your profile at %s was changed'), \Yii::app()->name));
            $mailer->send('update_user_info', array(
                'user' => $this,
            ));
        });

        $this->onAfterInsert(function() {
            $mailer = \Yii::app()->mailer;
            $mailer->addAddress($this->getEmail(), $this->getName());
            $mailer->setSubject(_('Registration'));
            $mailer->send('after_registration', array(
                'user' => $this,
            ));
        });
    }

    public function __sleep()
    {
        return array('_data');
    }

    public function rules()
    {
        return array(
            array('name, email', 'required', 'message' => _('This field required')),
            array('password', 'required', 'on' => 'register', 'message' => _('This field required')),
            array('email', 'email', 'message' => _('Wrong E-mail')),
            array('email', 'uniqueAttributeValidator', 'message' => _('User with specified E-mail already registered')),
        );
    }

    public function uniqueAttributeValidator($attribute, $params)
    {
        $userSearch = \Yii::app()->mongo
            ->getCollection('users')
            ->find()
            ->notDeleted()
            ->where($attribute, $this->$attribute);

        if($this->getId()) {
            $userSearch->exceptId($this->getId());
        }
        
        return $userSearch->count() === 0;
    }

    public function setEmail($email)
    {
        $this->set('email', $email);
        return $this;
    }

    public function getEmail()
    {
        return $this->get('email');
    }

    public function setPassword($password)
    {
        if ($this->isPasswordEquals($password)) {
            return $this;
        }

        $this->_changedPassword = $password;

        $salt = uniqid();
        $hash = $this->getPasswordHash($password, $salt);

        $this->set('password', $hash);
        $this->set('salt', $salt);

        return $this;
    }

    public function getChangedPassword()
    {
        return $this->_changedPassword;
    }

    public function isPasswordChanged()
    {
        return (bool) $this->_changedPassword;
    }

    public static function getPasswordHash($password, $salt)
    {
        $salt = '$2y$07$' . str_pad(substr($salt, 0, 22), 22, '0') . '$';

        return crypt($password, $salt);
    }

    public function getPassword()
    {
        return $this->get('password');
    }

    public function isPasswordEquals($password)
    {
        return $this->get('password') === $this->getPasswordHash($password, $this->get('salt'));
    }

    public function getRole()
    {
        return $this->get('role');
    }

    /**
     * Check if user has specified role
     * 
     * @param type $role
     * @return type
     */
    public function hasRole($role)
    {
        return $role == $this->getRole();
    }

    public function setRole($role)
    {
        $this->set('role', $role);
        
        return $this;
    }

    public function isManager()
    {
        return $this->hasRole(self::ROLE_MANAGER);
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function setName($name)
    {
        $this->set('name', $name);
        return $this;
    }

    public function getPhone()
    {
        return $this->get('phone');
    }

    public function setPhone($phone)
    {
        $this->set('phone', $phone);
        return $this;
    }

    public function setAdvertiser(Advertiser $advertiser)
    {
        $this->set('advertiser', $advertiser->getId());
        
        $this->setRole('advertiser');
        
        return $this;
    }

    public function getAdvertiserId()
    {
        return (string) $this->get('advertiser');
    }

    /**
     * Check if user is advertiser's manager
     */
    public function isAdvertiserManager()
    {
        return $this->hasRole(self::ROLE_ADVERTISER);
    }

    public function setPartner(Partner $partner)
    {
        $this->set('partner', $partner->getId());

        $this->setRole('partner');

        return $this;
    }

    public function getPartnerId()
    {
        return (string) $this->get('partner');
    }

    /**
     * Check if user is partner's manager
     */
    public function isPartnerManager()
    {
        return $this->hasRole(self::ROLE_PARTNER);
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

    public function setDeleted()
    {
        $this->set('status', self::STATUS_DELETED);
        return $this;
    }

    public function isDeleted()
    {
        return self::STATUS_DELETED === $this->get('status');
    }

    public function isEmailEquals($email)
    {
        return $this->get('email') === $email;
    }
    
    public function canBeManagedBy($user)
    {
        if($user instanceof \User) {
            $user = $user->getProfile();
        } elseif (!($user instanceof \MongoAdvertDb\Users\User)) {
            throw new \Excepption('Wrong user passed');
        }

        // user must have permission to manage users
        if( !\Yii::app()->getAuthManager()->checkAccess('manageUser', (string) $user->getId()) ) {
            return false;
        }

        if ( $user->isAdvertiserManager() && \Yii::app()->getAuthManager()->checkAccess('manageUser.editAdvertiser', (string) $user->getId()) ) {
            // user must belongs to same advertiser
            return $user->getAdvertiserId() === $this->getAdvertiserId();
        } elseif ( $user->isPartnerManager() && \Yii::app()->getAuthManager()->checkAccess('manageUser.editPartner', (string) $user->getId()) ) {
            // user must belongs to same partner
            return $user->getPartnerId() === $this->getPartnerId();
        } elseif ( $user->isManager() ) {
            return \Yii::app()->getAuthManager()->checkAccess('manageUser.editManager', (string) $user->getId());
        } else {
            return false;
        }
    }

}

<?php

namespace MongoAdvertDb\Users;

use \MongoAdvertDb\Advertisers\Advertiser;
use \MongoAdvertDb\Partners\Partner;

class QueryExpression extends \Sokil\Mongo\Expression
{

    public function byEmail($email)
    {
        $this->where('email', $email);
        return $this;
    }

    public function byAdvertiser(Advertiser $advertiser)
    {
        $this->where('advertiser', $advertiser->getId());
        return $this;
    }

    public function noAdvertiser()
    {
        $this->whereEmpty('advertiser');
        return $this;
    }

    public function byPartner(Partner $partner)
    {
        $this->where('partner', $partner->getId());
        return $this;
    }

    public function exceptId($id)
    {
        if (!($id instanceof \MongoId)) {
            $id = new \MongoId($id);
        }

        $this->whereNotEqual('_id', $id);
        return $this;
    }

    public function notDeleted()
    {
        $this->whereNotEqual('status', \MongoAdvertDb\Users\User::STATUS_DELETED);
        return $this;
    }
    
    public function active()
    {
        return $this->where('status', \MongoAdvertDb\Users\User::STATUS_ACTIVE);
    }

    public function byManagerRole()
    {
        $this->where('role', \MongoAdvertDb\Users\User::ROLE_MANAGER);
        return $this;
    }

    public function byAdvertiserRole()
    {
        $this->where('role', \MongoAdvertDb\Users\User::ROLE_ADVERTISER);
        return $this;
    }

    public function byPartnerRole()
    {
        $this->where('role', \MongoAdvertDb\Users\User::ROLE_PARTNER);
        return $this;
    }

}

<?php

namespace MongoAdvertDb\Advertisers;

use MongoAdvertDb\Advertisers\Advertiser;

class QueryExpression extends \Sokil\Mongo\Expression
{
    public function byNotEmptyCredit()
    {
        $this->where('bc_credit', array('$gt' => 0));
        return $this;
    }
    
    public function byAdvertiserNotEqualsTo(Advertiser $advertiser)
    {
        $this->where('_id', array('$ne' => $advertiser->getId()));
        return $this;
    }
    
    public function notDeleted()
    {
        $this->where('status', array('$ne' => \MongoAdvertDb\Advertisers\Advertiser::STATUS_DELETED));
        return $this;
    }
    
    public function byNameLike($name) 
    {
        $this->where('name', array('$regex' => $name, '$options' => 'i'));
        return $this;
    }    
}
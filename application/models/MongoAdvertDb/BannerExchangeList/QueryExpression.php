<?php

namespace MongoAdvertDb\BannerExchangeList;

use MongoAdvertDb\Advertisers\Advertiser;

class QueryExpression extends \Sokil\Mongo\Expression
{
    public function byAdvertiser(Advertiser $advertiser)
    {
        $this->where('advertiser',  $advertiser->getId());
        return $this;
    }
}
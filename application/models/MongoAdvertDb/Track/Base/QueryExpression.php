<?php

namespace MongoAdvertDb\Track\Base;

use \MongoAdvertDb\Visitors\Visitor;
use \MongoAdvertDb\Banners\Banner;

abstract class QueryExpression extends \Sokil\Mongo\Expression {

    public function byVisitor(Visitor $visitor) {
        $this->where('visitor', $visitor->getId());
        return $this;
    }

    public function byBanner(Banner $banner) {
        $this->where('banner.id', $banner->getId());
        return $this;
    }

    public function byDateGreaterThan($time) {
        $this->where('date', [
            '$gt' => new \MongoDate($time)
            ]);
        return $this;
    }

}

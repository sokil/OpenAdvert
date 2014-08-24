<?php
namespace MongoAdvertDb\Partners;

use MongoAdvertDb\Partners\Partner;

class QueryExpression extends \Sokil\Mongo\Expression
{
    public function notDeleted()
    {
        $this->whereNotEqual('status', \MongoAdvertDb\Users\User::STATUS_DELETED);
        return $this;
    }
    
    public function byRef($ref)
    {
        return $this->where('ref', $ref);
    }

    public function active()
    {
        return $this->where('status', Partner::STATUS_ACTIVE);
    }
}
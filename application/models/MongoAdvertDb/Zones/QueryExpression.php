<?php

namespace MongoAdvertDb\Zones;

use MongoAdvertDb\Zones\Zone;

class QueryExpression extends \Sokil\Mongo\Expression
{
    public function notDeleted() 
    {
        $this->whereNotEqual('status', Zone::STATUS_DELETED);
        return $this;
    }

    public function active() 
    {
        $this->where('status', Zone::STATUS_ACTIVE);
        return $this;
    }

    public function byType($type)
    {
        $this->where('type', $type);
        return $this;
    }
    
    public function byNameLike($name) 
    {
        $this->where('name', array('$regex' => $name, '$options' => 'i'));
        return $this;
    }
}

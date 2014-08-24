<?php

namespace MongoAdvertDb\Apikeys;

class QueryBuilder extends \Sokil\Mongo\QueryBuilder
{
    public function byName($name)
    {
        return $this->where('name', $name);
    }
}
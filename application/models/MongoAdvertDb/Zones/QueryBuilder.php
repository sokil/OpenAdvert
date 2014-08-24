<?php

namespace MongoAdvertDb\Zones;

class QueryBuilder extends \Sokil\Mongo\QueryBuilder
{
    public function sortByName()
    {
        $this->sort(array('name' => 1));
        return $this;
    }
}


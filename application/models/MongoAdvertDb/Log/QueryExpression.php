<?php

namespace MongoAdvertDb\Log;

class QueryExpression extends \Sokil\Mongo\Expression
{

    public function fromDate($date)
    {
        $this->whereGreaterOrEqual('logtime', new \MongoDate(strtotime($date . ' 00:00:00')));
        return $this;
    }

    public function toDate($date)
    {
        $this->whereLessOrEqual('logtime', new \MongoDate(strtotime($date . ' 23:59:59')));
        return $this;
    }

}

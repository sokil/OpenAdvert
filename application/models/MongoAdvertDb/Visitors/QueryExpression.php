<?php
namespace MongoAdvertDb\Visitors;

use \MongoAdvertDb\Partners\Partner;

class QueryExpression extends \Sokil\Mongo\Expression
{

    public function byPartner(Partner $partner)
    {
        $this->where('ref', $partner->getRef());
        return $this;
    }
    
    public function byLastActiveLessThen($date)
    {
        if (!is_numeric($date)) {
            $date = strtotime($date);
            if(!$date) {
                throw new \Exception('Wrong date specified');
            }
        }
        
        $this->whereLess('lastActive', new \MongoDate($date));
        return $this;
    }

}
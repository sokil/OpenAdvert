<?php

namespace MongoAdvertDb\Banners\DeliveryOptions\Option;

use \MongoAdvertDb\Banners\DeliveryOptions\MultiValueOption;
use \Sokil\Mongo\QueryBuilder;

class WeekDayOption extends MultiValueOption {

    public function getCaption() {
        return _('Week day');
    }

    public function getAvailableValues() {
        return array(
            1 => _('Monday'),
            2 => _('Tuesday'),
            3 => _('Wednesday'),
            4 => _('Thursday'),
            5 => _('Friday'),
            6 => _('Saturday'),
            7 => _('Sunday'),
        );
    }

    public function getCompareExpressions(QueryBuilder $builder)
    {
        $value = date('N');

        return array_map(function($comparisonRule) use($builder, $value) {
            switch ($comparisonRule) {
                case self::COMPARISON_NONE_OF:
                    return $builder->expression()->optionValueNoneOf($this->getType(), $value);
                    
                case self::COMPARISON_ANY_OF:
                default:
                    return $builder->expression()->optionValueAnyOf($this->getType(), $value);
            }
        }, array_keys($this->getComparisons()));
    }

}

<?php

namespace MongoAdvertDb\Banners\DeliveryOptions\Option;

use \MongoAdvertDb\Banners\DeliveryOptions\MultiValueOption;
use \Sokil\Mongo\QueryBuilder;

class CountryOption extends MultiValueOption
{
    protected $_type = self::TYPE_SELECT;
    
    public function getCaption()
    {
        return _('Country');
    }

    public function getAvailableValues() 
    {
        $isoCodes = new \Sokil\IsoCodes();
        $countries = array_map(function($country) {
            return $country->getLocalName();
        }, $isoCodes->getCountries()->toArray());
        
        asort($countries);
        
        return $countries;
    }
    
    public function getCompareExpressions(QueryBuilder $builder)
    {
        $ip = \Yii::app()->getRequest()->getUserHostAddress();
        if(0 === strpos($ip, '127') || 0 === strpos($ip, '192')) {
            return false;
        }
        
        $value = strtolower(@geoip_country_code_by_name($ip));
        if(!$value) {
            return false;
        }

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

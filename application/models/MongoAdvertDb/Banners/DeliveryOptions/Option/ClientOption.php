<?php
namespace MongoAdvertDb\Banners\DeliveryOptions\Option;

use \MongoAdvertDb\Banners\DeliveryOptions\MultiValueOption;
use \Sokil\Mongo\QueryBuilder;

class ClientOption extends MultiValueOption
{    
    public function getCaption()
    {
        return _('Client');
    }

    public function getAvailableValues()
    {
        $values = array(
            'Chrome' => _('Chrome'),
            'Firefox' => _('Firefox'),
            'Opera' => _('Opera'),
            'IE' => _('IE'),
        );

        return $values;

    }

    public function getCompareExpressions(QueryBuilder $builder)
    {
        $userAgent = \Yii::app()->getRequest()->getUserAgent();

        $value = get_browser($userAgent)->browser;
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
<?php

namespace MongoAdvertDb\Banners\DeliveryOptions\Option;

use \MongoAdvertDb\Banners\DeliveryOptions\MultiValueOption;
use \Sokil\Mongo\QueryBuilder;

class OperatingSystemOption extends MultiValueOption
{   
    public function getCaption() {
        return _('Operating System');
    }

    public function getAvailableValues() {
        return array(
            'win'           => _('Any Windows'),
            'winXP'         => _('Windows XP'),
            'win7'          => _('Windows 7'),
            'win8'          => _('Windows 8'),
            
            'winphone'      => _('Windows Phone'),
            
            'linux'         => _('Linux'),
            
            'ubuntu'        => _('Ubuntu'),
            
            'freebsd'       => _('FreeBSD'),
            
            'android'       => _('Any Android'),
            'androidphone'  => _('Android Phone'),
            'androidtablet' => _('Android Tablet'),
            'android2'      => _('Android 2'),
            'android3'      => _('Android 3'),
            'android4'      => _('Android 4'),
            
            'osx'           => _('Any Apple OS X'),
            'osx10.7'       => _('Apple OS X 10.7 Lion'),
            'osx10.8'       => _('Apple OS X 10.8 Mountain Lion'),
            'osx10.9'       => _('Apple OS X 10.9 Mavericks'),
            
            'ios'           => _('Any Apple iOS'),
            'ios1'          => _('Apple iOS 1'),
            'ios2'          => _('Apple iOS 2'),
            'ios3'          => _('Apple iOS 3'),
            'ios4'          => _('Apple iOS 4'),
            'ios5'          => _('Apple iOS 5'),
            'ios6'          => _('Apple iOS 6'),
            'ios7'          => _('Apple iOS 7'),
            
            'iphone'        => _('Apple iPhone'),
            'iphone1'       => _('Apple iPhone 1'),
            'iphone2'       => _('Apple iPhone 2'),
            'iphone3'       => _('Apple iPhone 3'),
            'iphone4'       => _('Apple iPhone 4'),
            'iphone5'       => _('Apple iPhone 5'),
            
            'ipad'          => _('Apple iPad'),
            'ipad1'         => _('Apple iPad 1'),
            'ipad2'         => _('Apple iPad 2'),
            'ipad3'         => _('Apple iPad 3'),
            'ipad4'         => _('Apple iPad 4'),
        );
    }
    
    public function getCompareExpressions(QueryBuilder $builder)
    {
        $ua = \Yii::app()->getRequest()->getUserAgent();
        if (isset($ua)) {
            $browser = get_browser($ua);
        } else {
            $browser = get_browser('');
        }

        switch ($browser->platform) {
            case 'iOS':
                $user = $browser->device_name . floor($browser->platform_version);
                break;
            case 'Android':
                $user = $browser->platform . floor($browser->platform_version);
                break;
            default:
                $user = $browser->platform;
        }
        
        $values = array('unknown');
        foreach (array_keys($this->getAvailableValues()) as $value) {
            if (stripos($user, $value)!==false) {
                $values[] = $value;
            }
        }
        
        if(!$value) {
            return false;
        }

        return array_map(function($comparisonRule) use($builder, $values) {
            switch ($comparisonRule) {
                case self::COMPARISON_NONE_OF:
                    return $builder->expression()->optionValueNoneOf($this->getType(), $values);
                    
                case self::COMPARISON_ANY_OF:
                default:
                    return $builder->expression()->optionValueAnyOf($this->getType(), $values);
            }
        }, array_keys($this->getComparisons()));
    }
}

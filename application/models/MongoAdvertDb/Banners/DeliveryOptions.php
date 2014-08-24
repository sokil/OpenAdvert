<?php

namespace MongoAdvertDb\Banners;

use \MongoAdvertDb\Banners\DeliveryOptions\Renderer;

class DeliveryOptions
{
    private static $_availableOptions;
    
    protected static $_renderer;
    
    /**
     * 
     * @param string $type
     * @return \MongoAdvertDb\Banners\DeliveryOptions\Option
     * @throws \Exception
     */
    public function create($type)
    {
        $className = '\\MongoAdvertDb\\Banners\\DeliveryOptions\\Option\\' . ucfirst($type) . 'Option';
        if(!class_exists($className)) {
            throw new \Exception('Wrong option type specified');
        }
        
        return new $className;
    }
    
    public static function getAvailableOptions()
    {
        if(null !== self::$_availableOptions) {
            return self::$_availableOptions;
        }
        
        $list = glob(__DIR__ . '/DeliveryOptions/Option/*Option.php');
        
        if(!$list) {
            self::$_availableOptions = array();
        }
        else {
            self::$_availableOptions = array_map(function($path) {
                return substr(pathinfo($path, PATHINFO_FILENAME), 0, -6);
            }, $list);
        }
        
        return self::$_availableOptions;
    }
    
    /**
     * 
     * @return \MongoAdvertDb\Banners\DeliveryOptionRenderer
     */
    public static function getRenderer()
    {
        if(!self::$_renderer) {
            self::$_renderer = new Renderer;
        }
        
        return self::$_renderer;
    }
}
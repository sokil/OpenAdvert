<?php

namespace MongoAdvertDb\Banners;

class DeliveryOptionRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \MongoAdvertDb\Banners\DeliveryOptionRenderer
     */
    private static $_renderer;
    
    public static function setUpBeforeClass() {
        self::$_renderer = new DeliveryOptions;
    }
    
    public function testGetAvailableOptions()
    {
        $availableOptions = self::$_renderer
            ->getAvailableOptions();
        
        $this->assertInternalType('array', $availableOptions);
        
        $this->assertNotEmpty($availableOptions);
        
        foreach($availableOptions as $optionType) {
            
            $this->assertInstanceOf(
                '\MongoAdvertDb\Banners\DeliveryOptions\Option', 
                \Yii::app()->mongo->getCollection('banners')->getDeliveryOptions()->create($optionType)
            );
        }
    }
    
}

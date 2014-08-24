<?php

namespace MongoAdvertDb;

class BannersTest extends \PHPUnit_Framework_TestCase {
    
    public static function setUpBeforeClass() {
        $_GET['key'] = 'value';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPad; CPU OS 5_1_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B206 Safari/7534.48.3';
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3';
        $_GET['mw_vod'] = 54;
        $_GET['mw_vod_cat'] = 3;
        $_GET['mw_vod_gen'] = '96,69';
        $_GET['mw_ch_cat'] = 1;
        $_GET['mw_ch'] = 554;
        $_GET['ref'] = 'fer';
    }

    /**
     * @covers MongoAdvertDb\Banners::getRandomBannerOfZone
     */
    public function testGetRandomBannerOfZone() {
        $database = \Yii::app()->mongo;

        $zone = $database->getCollection('zones')
            ->find()
            ->byNameLike('Video Zone 1')
            ->findOne();
        if (!$zone) {
            $this->fail('no zone found');
        }
        
        $banners = $database->getCollection('banners');
        $banner = $banners
            ->find()
            ->byNameLike('Video Banner 1')
            ->findOne();
        if (!$banner) {
            $this->fail('no banner found');
        }
        
        $options = array(
            array(
                'option' => 'weekDay',
                'comparison' => '=~',
                'value' => array(date('N')),
            ),
            array(
                'option' => 'variable',
                'comparison' => '==',
                'value' => array('key' => 'key', 'value' => 'value'),
            ),
            array(
                'option' => 'time',
                'comparison' => '==',
                'value' => array('minh' => date('H'), 'minm' => date('i'), 'maxh' => date('H'), 'maxm' => date('i')),
            ),
            array(
                'option' => 'operatingSystem',
                'comparison' => '=~',
                'value' => array('ipad'),
            ),
            array(
                'option' => 'language',
                'comparison' => '=~',
                'value' => array('en'),
            ),
            array(
                'option' => 'film',
                'comparison' => '==',
                'value' => 54,
            ),
            array(
                'option' => 'filmCategory',
                'comparison' => '==',
                'value' => '96',
            ),
            array(
                'option' => 'date',
                'comparison' => '==',
                'value' => date('Y-m-d'),
            ),
            array(
                'option' => 'country',
                'comparison' => '!~',
                'value' => 'il',
            ),
            array(
                'option' => 'channel',
                'comparison' => '==',
                'value' => 554,
            ),
            array(
                'option' => 'channelCategory',
                'comparison' => '==',
                'value' => 1,
            ),
            array(
                'option' => 'ref',
                'comparison' => '==',
                'value' => 'fer',
            ),
        );

        foreach ($options as $option) {
            $o = $banners->getDeliveryOptions()->create($option['option']);
            $o->setValue($option['value'], $option['comparison']);
            $banner->setDeliveryOptions(array($o));
            $banners->saveDocument($banner);
            
            $randomBanner = $banners->getRandomBannerOfZone($zone);
            if (!$randomBanner) {
                $this->fail('No random banner found. Option: ' . $option['option']);
            }

            $this->assertEquals($banner, $randomBanner, 'Banners are not equal. Option: ' . $option['option']);
        }
    }

}

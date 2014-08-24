<?php

namespace MongoAdvertDb\Banners\Banner\VideoBanner;

class MediaFileTest extends \PHPUnit_Framework_TestCase
{
    public function testSetUrl()
    {
        $mediaFile = MediaFile::create()
            ->setUrl('/test/test.mp4');
        
        $this->assertEquals(6, $mediaFile->getDuration());
        $this->assertEquals(720, $mediaFile->getHeight());
        $this->assertEquals(1280, $mediaFile->getWidth());
        $this->assertEquals('video/mp4', $mediaFile->getType());
    }
}
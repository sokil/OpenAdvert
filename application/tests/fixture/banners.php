<?php

return array(
    array(
        "_id" => new MongoId("52cbee3841a88f7611e48222"),
        "campaign" => new MongoId("52bbc09041a88f23123cd139"),
        "deliveryOptions" => array(),
        "duration" => 60,
        "events" => array(),
        "mediaFiles" => array(
            array(
                "delivery" => "progressive",
                "type" => "video/mp4",
                "height" => 720,
                "width" => 576,
                "url" => "http=>//st10.cdn.holder.com.ua/h/1/7/473_0.mp4"
            ),
        ),
        "name" => "Video Banner 1",
        "status" => "ACTIVE",
        "type" => "video",
        "url" => "http=>//example.com/video-banner-1",
        "zones" => array(
            new MongoId("52cbeddc41a88ffe111ab11b"),
            )
    ),
    array(
        "_id" => new MongoId("52cbf4ce41a88ffe1191a83c"),
        "campaign" => new MongoId("52bbc09041a88f23123cd139"),
        "deliveryOptions" => array(),
        "duration" => 60,
        "events" => array(),
        "mediaFiles" => array(
            array(
                "delivery" => "streaming",
                "type" => "video/mp4",
                "height" => 720,
                "width" => 576,
                "url" => "http=>//ytv.su/ad/ad1.mp4"
            ),
        ),
        "name" => "Video Banner 2",
        "status" => "ACTIVE",
        "type" => "video",
        "url" => "http=>//example.com/video-banner-2",
        "zones" => array()
    ),
);

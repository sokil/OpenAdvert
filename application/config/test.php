<?php

return CMap::mergeArray(
        require(dirname(__FILE__) . '/main.php'), array(
        'components' => array(
            'mongo' => array(
                'defaultDatabase' => 'testbase',
                'map' => array(
                    'testbase' => 'advert'
                ),
            ),
        ),
        )
);

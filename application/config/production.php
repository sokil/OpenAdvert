<?php

return CMap::mergeArray(
    require(APPLICATION_PATH . '/config/main.php'), array(
        'components' => array(
            'packages'  => array(
                'jquery' => array(
                    'baseUrl'               => 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2',
                    'js'                    => array('jquery.min.js'),
                    'coreScriptPosition'    => CClientScript::POS_HEAD,
                ),
            ),
            'clientScript' => array(
                'scriptMap' => array(
                    'form.js'                   => '/js/main.js',
                    
                    'bootstrap.min.js'          => '/js/main.js',
                    'bootstrap.min.css'         => '/css/main.css',
                    'ui.css'                    => '/css/main.css',

                    'jquery.pickmeup.min.js'    => '/js/main.js',
                    'pickmeup.min.css'          => '/css/main.css',
                    
                    'common.js'                 => '/js/main.js',
                    
                    'bannerEditor.js'           => '/js/main.js',
                    'videoBannerEditor.js'      => '/js/main.js',
                    
                    'modal.js'                  => '/js/main.js',
                    
                    'stat.js'                   => '/js/stat.js',
                )
            ),
            'mailer' => array(
                'debug' => 0,
            ),
            'log'   => array(
                'routes' => array(
                    array(
                        'class'     => 'MongoLogRoute',
                        'levels'    => 'error, warning',
                    ),
                ),
            ),
            'mongo' => array(
                // disable logger on production
                'logger' => null,
            ),
        ),
        'params'    => array(
            // path to ffprobe
            'ffprobe_path'  => '/usr/local/bin/ffprobe',
        )
    )
);

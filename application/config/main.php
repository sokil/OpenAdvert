<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => APPLICATION_PATH,
    'name' => 'OpenAdvert',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.extensions.*',
        'application.widgets.*',
    ),
    'modules' => array(
        'api',
    ),
    'defaultController' => 'index',
    'components' => array(
        'format' => array(
            'class' => 'application.components.Formatter',
            'dateFormat' => 'm/d/Y',
            'datetimeFormat' => 'm/d/Y h:i:s A',
            'numberFormat' => array('decimals' => 2, 'decimalSeparator' => '.', 'thousandSeparator' => ',')
        ),
        'clientScript' => array(
            // http://yiiframework.ru/doc/cookbook/ru/js.package
            'packages' => array(
                'bootstrap' => array(
                    'baseUrl' => '',
                    'js' => array('js/bootstrap.min.js'),
                    'css' => array('css/bootstrap.min.css', 'css/ui.css'),
                    'depends' => array('jquery'),
                ),
                'pickmeup' => array(
                    'baseUrl' => '',
                    'js' => array('js/jquery.pickmeup.min.js'),
                    'css' => array('css/pickmeup.min.css'),
                    'depends' => array('jquery'),
                ),
                'typeahead' => array(
                    'baseUrl' => 'js/typeahead/',
                    'js' => array('bloodhound.min.js', 'typeahead.jquery.min.js'),
                    'css' => array('../../css/typeahead.js.css'),
                    'depends' => array('jquery'),
                ),
            ),
        ),
        'response' => array(
            'class' => 'Response',
        ),
        'user' => array(
            'class' => 'User',
            'allowAutoLogin' => true,
            'loginUrl' => array('/login'),
        ),
        'authManager' => array(
            'class' => 'PhpAuthManager',
            'defaultRoles' => array('guest'),
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                '/login' => '/users/login',
                '/logout' => '/users/logout',
                '/z/<id:\w+>' => '/zones/code',
                '<module:api>/<controller:\w+>/<action:\w+>/<id:\w+>' => '<module>/<controller>/<action>',
                '<module:api>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<id:\w+>' => '<controller>/<action>',
            ),
        ),
        'errorHandler' => array(
            'errorAction' => 'error/error',
        ),
        'session' => array(
            'sessionName' => 's',
            'autoStart' => false,
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                // log errors and warnings
                array(
                    'class'             => '\Sokil\Mongo\Yii\LogRoute',
                    'collectionName'    => 'log',
                    'serviceName'       => 'mongo',
                    'levels'            => 'error, warning',
                    'filter'            => array(
                        'class'         => 'LogCategoryFilter',
                        'categories'    => array(
                            'exception.CHttpException.404',
                        )
                    ),
                ),
                // write PHPMongo debug messages to log
                array(
                    'class' => '\Sokil\Mongo\Yii\LogRoute',
                    'collectionName' => 'log',
                    'serviceName' => 'mongo',
                    'categories' => 'PHPMongoLogger',
                ),
            ),
        ),
        'report' => array(
            'class' => 'ReportFactory',
        ),
        'mongo' => array(
            'class' => '\Sokil\Mongo\Yii\ClientAdapter',
            'dsn' => 'mongodb://127.0.0.1',
            'options' => array(
                'connect' => true,
                'readPreference' => \MongoClient::RP_SECONDARY_PREFERRED,
            ),
            'defaultDatabase' => 'advert',
            'map' => array(
                'advert' => array(
                    'visitors' => '\MongoAdvertDb\Visitors',
                    'zones' => '\MongoAdvertDb\Zones',
                    'advertisers' => '\MongoAdvertDb\Advertisers',
                    'partners' => '\MongoAdvertDb\Partners',
                    'campaigns' => '\MongoAdvertDb\Campaigns',
                    'banners' => '\MongoAdvertDb\Banners',
                    'bc_banners' => '\MongoAdvertDb\BannerExchangeList',
                    'bc_stat' => '\MongoAdvertDb\BannerExchangeStat',
                    'users' => '\MongoAdvertDb\Users',
                    'track.event' => '\MongoAdvertDb\Track\\Event',
                    'track.click' => '\MongoAdvertDb\Track\\Click',
                    'track.impression' => '\MongoAdvertDb\Track\\Impression',
                    'apikeys' => '\MongoAdvertDb\Apikeys',
                )
            ),
            'logger' => 'psrPHPMongoLogger',
        ),
        // this logger used as php-mongo logger for debuggings
        'psrPHPMongoLogger' => array(
            'class' => 'PsrLogAdapter',
            'category' => 'PHPMongoLogger',
        ),
        'translate' => array(
            'class' => 'Translate',
            'supportedLanguages' => array(
                'en' => 'en_US.UTF8',
                'uk' => 'uk_UA.UTF8',
                'ru' => 'ru_RU.UTF8'
            ),
            'defaultLanguage' => 'ru',
        ),
        'uploader' => array(
            'class' => '\Sokil\Uploader\Adapter\Yii\Uploader'
        ),
        'cache' => array(
            'class' => 'system.caching.CMemCache',
            'servers' => array(
                array('host' => 'localhost'),
            ),
            'useMemcached' => true,
        ),
        'mailer' => array(
            'class' => 'application.components.Mailer',
            'template' => 'application.views.mail',
            'debug' => 2,
            'host' => 'localhost',
            'port' => 25,
            'SMTPSecure' => null,
            'username' => null,
            'password' => null,
            'fromEmail' => null,
            'fromName' => 'OpenAdvert Support Team',
            'reply' => null,
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // path to banner files like images of videos, relative from bublic path
        'bannerStorageDir' => '/static',
        // path to ffprobe
        'ffprobe_path' => '/usr/bin/ffprobe',
        // XHProof cookie key. Must be overrided in production config file, if
        // xhproof required
        'xhproofCookieKey' => null
    ),
    'OnBeginRequest' => function() {

        // xhproof
        if(isset($_COOKIE[Yii::app()->params['xhprofCookieKey']])) {
            xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
        }

        /* @var $translate Translate */
        $translate = Yii::app()->translate;

        // Get lang marker
        $parts = explode('/', substr($_SERVER['REQUEST_URI'], 1));
        if ($parts && in_array($parts[0], array_keys($translate->supportedLanguages))) {
            // Remove lang marker
            $language = array_shift($parts);
            $_SERVER['REQUEST_URI'] = '/' . implode('/', $parts);

            // Set language
            $translate->setLanguage($language);
        }

        // Init localization
        Yii::app()->translate->initGettext('system', Yii::app()->basePath . '/messages/');
    },
    'OnEndRequest' => function() {
        // xhproof
        if(isset($_COOKIE[Yii::app()->params['xhprofCookieKey']])) {

            include_once __DIR__ . '/../extensions/xhprof/xhprof_lib/utils/xhprof_lib.php';
            include_once __DIR__ . '/../extensions/xhprof/xhprof_lib/utils/xhprof_runs.php';

            $xhprof_runs = new XHProfRuns_Default('/tmp');
            $xhprofData = xhprof_disable();

            $namespace = 'std';
            $runId = $xhprof_runs->save_run($xhprofData, $namespace);
            echo sprintf(
                '<a href="/xhprof_html/index.php?run=%s&source=%s">Xhprof</a>',
                $runId,
                $namespace
            );
        }
    }
);

<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
//            'class' => 'yii\web\Response',
//            'on beforeSend' => function ($event) {
//                $response = $event->sender;
//                $response->headers->add('Access-Control-Allow-Origin', 'http://localhost:5173/');
//                $response->headers->add('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
//                $response->headers->add('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
//            },
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Xr2lmhIFc7MjeeiBaksZQwL6377C99nD',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            // Set a custom error handler if not using site/error
            'errorAction' => null,
        ],
        'mailer' => [
            'class' => 'yii\symfonymailer\Mailer',
            'transport' => [
                'scheme' => 'smtp',
                'host' => 'smtp-relay.brevo.com',
                'username' => '75362d001@smtp-brevo.com',
                'password' => '2hM3OLNErSp15RJT',
                'port' => 587,
                'encryption' => 'tls',
            ],
            'useFileTransport' => false,
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'POST user/signup' => 'user/signup',
                'POST user/login' => 'user/login',
                'GET users/total' => 'user/userstotal',
                'GET counties' => 'county/showcounties',
                'POST add/county' => 'county/addcounty',
                'GET types' => 'types/showtypes',
                'POST add/type' => 'types/addtypes',
                'GET show/hosts' => 'hosts/showhosts',
                'POST add/host' => 'hosts/addhosts',
                'POST add/service' => 'services/addservice',

                'GET show/categories' => 'categories/showcategories',
                'POST add/category' => 'categories/addcategory',

                'GET show/accommodations' => 'categories/getaccommodations',
                'GET searchBy/service' => 'categories/searchtype',

                'GET show/roles' => 'roles/showroles',
                'POST add/role' => 'roles/addrole',

                'GET show/amenity' => 'roles/showamenities',

                'GET show/services' => 'services/getservices',
                'GET view/service' => 'services/viewservice',

                'GET get/orders' => 'orders/showorders',
                'POST place/order' => 'orders/ordernow',


                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;

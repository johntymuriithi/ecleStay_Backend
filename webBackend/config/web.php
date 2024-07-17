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
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest'],
        ],
         // only this
//        'corsFilter' => [
//            'class' => \yii\filters\Cors::class,
//            'cors' => [
//                'Origin' => ['*'], // Adjust as needed
//                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
//                'Access-Control-Allow-Credentials' => true,
//                'Access-Control-Max-Age' => 3600, // Cache for 1 hour
//                'Access-Control-Allow-Headers' => ['Content-Type', 'Authorization'],
//            ],
//        ],
        'response' => [
            // ...
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $response->headers->set('Access-Control-Allow-Origin',  '*');
                $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS'); // Allow all common HTTP methods
                $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With'); // Allow 'Content-Type' and 'Authorization' headers
            },
        ],

        'request' => [
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
            'enableAutoLogin' => false, // Set to true if you want to use cookie-based auth
            'enableSession' => false, // For stateless authentication like JWT
            'loginUrl' => null, // No redirect for API requests
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
            'hostInfo' => 'https://d147-41-90-101-26.ngrok-free.app', // very important for frontend
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

                'GET show/guides' => 'guides/showguides',
                'POST add/guide' => 'guides/addguides',

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
                'GET searchBy/county' => 'services/searchcounty',

                'GET get/orders' => 'orders/showorders',
                'POST place/order' => 'orders/ordernow',
                'GET get/active/users/guest' => 'orders/userguest',
                'GET get/guest/bookings' => 'orders/guestservices',


                'POST auth/permit/host' => 'permission/assignhost',
                'POST auth/permit/admin' => 'permission/assignadmin',
                'POST auth/permit/guide' => 'permission/assignguide',
                'POST auth/revoke/role' => 'permission/rolerevoker',

                'POST add/imager' => 'images/uploadimage',

                'POST review/host' => 'hoster/reviewhost',
                'POST review/service' => 'servicer/reviewservice',
                'GET show/host/reviews' => 'hoster/hostreviews',
                'GET show/service/reviews' => 'servicer/servicereviews',

                'GET analysis/host/services' => 'analysis/hostii',
                'GET analysis/host/approved' => 'analysis/approvedhosts',
                'GET analysis/host/waiting' => 'analysis/waitinghosts',
                'GET analysis/guideByCounty' => 'analysis/guidebycounty',
                'GET analysis/guides/approved' => 'analysis/approvedguides',
                'GET analysis/guides/waiting' => 'analysis/waitingguides',

                'GET tester' => 'analysis/tester',



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

<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;

class BaseController extends ActiveController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Enable JSON output
        $behaviors['contentNegotiator'] = [
            'class' => \yii\filters\ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

//        $behaviors['corsFilter'] = [
//            'class' => Cors::class,
//            'cors' => [
//                'Origin' => ['*'], // Replace * with your specific origins
//                'Access-Control-Request-Method' => ['POST', 'GET', 'OPTIONS', 'DELETE', 'PUT'],
//                'Access-Control-Allow-Credentials' => true,
//                'Access-Control-Max-Age' => 3600,
//                'Access-Control-Allow-Headers' => ['Content-Type', 'Authorization'],
//            ],
//        ];


        //        // JWT Authentication (placed after access control) // incase it fails,,please login 401
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['login', 'signup', 'viewservice', 'showcategories', 'addcategory', 'servicereviews',
                'getaccommodations', 'searchtype', 'showtypes', 'addhosts', 'hostreviews',
                'showcounties', 'getservices', 'resetpasswordlink', 'resetpassword', 'hostreviews', 'addcounty', 'searchcounty'], // Actions that don't require authentication
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['login', 'signup', 'getservices', 'viewservice',
                        'showcategories', 'addcategory', 'getaccommodations', 'searchtype',
                        'showtypes', 'addhosts', 'showcounties', 'resetpasswordlink',
                        'resetpassword', 'hostreviews', 'addcounty', 'servicereviews', 'searchcounty'],
                    'roles' => ['?'], // Allow guests (unauthenticated users) // in short in mean users
                ],
                [
                    'allow' => true,
                    'actions' => ['showtypes', 'ordernow', 'addhosts', 'toa', 'showcategories', 'reviewhost',
                        'addservice', 'hostrevews', 'reviewservice', 'showorders', 'servicereviews', 'hostreviews', 'guestservices'],
                    'roles' => ['@'], // authenticated users only // passed the bearer auth
                ],
                [
                    'allow' => true,
                    'actions' => ['showhosts', 'showcounties', 'addcounty', 'addtypes',
                        'addservice', 'getaccommodations', 'reviewhost', 'hostii', 'userguest',
                        'rolerevoker', 'assignadmin', 'assignhost', 'approvedhosts', 'waitinghosts', "userstotal"],
                    'roles' => ['admin'], // Require admin role
                ],
                [
                    'allow' => true,
                    'actions' => ['hostii', 'tester'],
                    'roles' => ['host'], // Require user role
                ],
            ],
            'denyCallback' => function ($rule, $action) {
                throw new \yii\web\ForbiddenHttpException('Sorry,You are not Allowed to Access This ACTION.');
            },
        ];

        return $behaviors;
    }
}
?>
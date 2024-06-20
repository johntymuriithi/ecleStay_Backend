<?php

namespace app\controllers;

use yii\filters\AccessControl;
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

        //        // JWT Authentication (placed after access control) // incase it fails,,please login 401
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['login', 'signup', 'addservice', 'viewservice', 'showcategories', 'addcategory', 'getaccommodations', 'searchtype', 'showtypes', 'addtypes', 'addhosts', 'showhosts', 'showcounties', 'getservices'], // Actions that don't require authentication
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['login', 'signup', 'addservice', 'getservices', 'viewservice', 'showcategories', 'addcategory', 'getaccommodations', 'searchtype', 'showtypes', 'addtypes', 'addhosts', 'showhosts', 'showcounties'],
                    'roles' => ['?'], // Allow guests (unauthenticated users) // in short in mean users
                ],
                [
                    'allow' => true,
                    'actions' => ['showtypes', 'ordernow', 'addhosts', 'toa', 'showcategories'],
                    'roles' => ['@'], // authenticated users only // passed the bearer auth
                ],
                [
                    'allow' => true,
                    'actions' => ['showhosts', 'showcounties', 'addcounty', 'addtypes', 'addservice', 'getaccommodations'],
                    'roles' => ['admin'], // Require admin role
                ],
                [
                    'allow' => true,
                    'actions' => [''],
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
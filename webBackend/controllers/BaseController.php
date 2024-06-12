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
            'except' => ['addservice', 'signup', 'login', 'getaccommodations', 'getservices', 'showcategories', 'showcounties', 'viewservice', 'toa', 'uploadimage'], // Actions that don't require authentication
        ];
        // Access control (placed before authenticator)
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['addservice', 'getaccommodations', 'getservices', 'showcategories', 'showcounties' , 'signup', 'login', 'viewservice', 'toa', 'uploadimage'],
                    'roles' => ['?'], // Allow guests (unauthenticated users) // in short in mean users
                ],
                [
                    'allow' => true,
                    'actions' => ['ordernow'],
                    'roles' => ['@'], // authenticated users only // passed the bearer auth
                ],
                [
                    'allow' => true,
                    'actions' => ['showhosts'],
                    'roles' => ['admin'], // Require admin role
                ],
                [
                    'allow' => true,
                    'actions' => ['showcounties'],
                    'roles' => ['host'], // Require user role
                ],
            ],
            'denyCallback' => function ($rule, $action) {
                throw new \yii\web\ForbiddenHttpException('No roles set for you.');
            },
        ];

        return $behaviors;
    }
}
?>
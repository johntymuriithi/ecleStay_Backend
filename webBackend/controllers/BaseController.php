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

        // Access control (placed before authenticator)
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['login', 'signup'],
                    'roles' => ['?'], // Allow guests (unauthenticated users)
                ],
                [
                    'allow' => true,
                    'actions' => ['getservices'],
                    'roles' => ['admin'], // Require admin role
                ],
                [
                    'allow' => true,
                    'actions' => ['user-only-action'],
                    'roles' => ['user'], // Require user role
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['createService'], // Require createService role
                ],
                // Optionally, add more rules for other roles and actions
            ],
        ];

        // JWT Authentication (placed after access control)
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['login', 'signup'], // Actions that don't require authentication
        ];

        return $behaviors;
    }
}
?>
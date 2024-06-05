<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\web\Response;

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

        // Add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => ['Origin' => ['*'], // Replace with your frontend URL'Access-Control-Request-Method' => ['GET', POST', 'PUT', 'DELETE'],
            'Access-Control-Allow-Credentials' => true,
                'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
            'Access-Control-Max-Age' => 3600, // Allow OPTIONS caching
            'Access-Control-Request-Headers' => ['*'],
            'Access-Control-Allow-Headers' => ['Content-Type' => 'application/json', 'Authorization'],
        ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        // Disable default actions if needed
        unset($actions['create'], $actions['update'], $actions['delete'], $actions['view']);
        return $actions;
    }
}

?>
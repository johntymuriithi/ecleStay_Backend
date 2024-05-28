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
            'cors' => [
                'Origin' => (YII_ENV_PROD) ? [''] : ['http://localhost:5174'], '*',
                'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
                'Access-Control-Request-Headers' => ['X-Wsse', 'Content-Type', '*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 3600,
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
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
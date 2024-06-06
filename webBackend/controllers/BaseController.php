<?php

namespace app\controllers;

use yii\filters\auth\HttpBearerAuth;
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
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['login', 'signup', 'showcounties', 'getservices'],
        ];
        return $behaviors;
    }
//
//    public function actions()
//    {
//        $actions = parent::actions();
//        // Disable default actions if needed
//        unset($actions['create'], $actions['update'], $actions['delete'], $actions['view']);
//        return $actions;
//    }

//    public function behaviors() {
//        return [
//            'corsFilter' => [
//                'class' => \yii\filters\Cors::className(),
//                'cors' => [
//                    // restrict access to
////                    'Origin' => (YII_ENV_PROD) ? [''] : ['*'],
//                    // Allow only POST and PUT methods
//                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
//                    // Allow only headers 'X-Wsse'
//                    'Access-Control-Request-Headers' => ['X-Wsse', 'Content-Type'],
//                    // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
//                    'Access-Control-Allow-Credentials' => true,
//                    // Allow OPTIONS caching
//                    'Access-Control-Max-Age' => 3600,
//                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
//                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
//                ],
//            ],
//        ];
//    }
}

?>
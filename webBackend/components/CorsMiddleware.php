<?php
//
//namespace app\components;
//
//use yii\base\ActionFilter;
//
//class CorsMiddleware extends ActionFilter
//{
//    public function beforeAction($action)
//    {
//        if (\Yii::$app->request->isOptions) {
//            \Yii::$app->response->headers->add('Access-Control-Allow-Origin', '*');
//            \Yii::$app->response->headers->add('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
//            \Yii::$app->response->headers->add('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
//            \Yii::$app->response->statusCode = 200;
//            \Yii::$app->response->send();
//            return false;
//        }
//
//        return parent::beforeAction($action);
//    }
//}
//

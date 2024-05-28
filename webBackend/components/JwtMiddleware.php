<?php
//
//namespace app\components;
//
//use Yii;
//use yii\base\ActionFilter;
//use yii\web\UnauthorizedHttpException;
//use app\models\User;
//
//class JwtMiddleware extends ActionFilter
//{
//    public function beforeAction($action)
//    {
//        $headers = Yii::$app->request->headers;
//        $authHeader = $headers->get('Authorization');
//
//        var_dump($authHeader);
//
//        if ($authHeader && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
//            $token = $matches[1];
//            if ($decoded = User::validateJwt($token)) {
//                return parent::beforeAction($action);
//            }
//        }
//        throw new UnauthorizedHttpException('Your request was made with invalid credentials.');
//    }
//}
//
//?>
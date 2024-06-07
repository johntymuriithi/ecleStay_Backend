<?php
//
//namespace app\controllers;
//
//use Yii;
//use yii\rest\Controller;
//use yii\web\Response;
//
//class PermissionController extends BaseController
//{
//    public function actionAssignCreateService($userId)
//    {
//        $auth = Yii::$app->authManager;
//        $createService = $auth->getPermission('createService');
//
//        if ($auth->assign($createService, $userId)) {
//            return [
//                'success' => true,
//                'message' => 'Permission "createService" assigned to user.',
//            ];
//        } else {
//            return [
//                'success' => false,
//                'message' => 'Failed to assign permission.',
//            ];
//        }
//    }
//}
//
//
//?>
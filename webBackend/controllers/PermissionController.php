<?php

namespace app\controllers;

use app\models\Hosts;
use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class PermissionController extends BaseController
{
    public $modelClass = 'app\models\Services';

    public function actionAssignhost($userId)
    {
        $user = User::findOne(['id' => $userId]);
        if (!$user) {
            throw new ForbiddenHttpException("Please Sign Up first to proceed with this Request");
        }
        $email = $user->email;
        $userHost = Hosts::findOne(['email' => $email]);
        if (!$userHost) {
            Yii::$app->response->statusCode = 403;
            return [
                'status' => 403,
                'message' => 'Please show your Interest on This Role by first registering to become a Host',
            ];
        }
        $auth = Yii::$app->authManager;
        // we first check if the user already has the role
        $isHe = $auth->getAssignment('host', $userId);
        if ($isHe) {
            throw new BadRequestHttpException("User already has the ROLE");
        }
        $userRole = $auth->getRole('host');
        if (!$auth->assign($userRole, $userId)) {
            throw new BadRequestHttpException("Failed to assign the to this User, please try again");
        }
        return ["status" => 200, 'Message' => "Role HOST assigned successfully"];
    }
    public function actionAssignadmin($userId)
    {
        $user = User::findOne(['id' => $userId]);
        if (!$user) {
            throw new ForbiddenHttpException("Please Sign Up first to proceed with this Request");
        }
        $auth = Yii::$app->authManager;
        // we first check if the user already has the role
        $isHe = $auth->getAssignment('admin', $userId);
        if ($isHe) {
            throw new BadRequestHttpException("User already has the ROLE");
        }
        $userRole = $auth->getRole('admin');
        if (!$auth->assign($userRole, $userId)) {
            throw new BadRequestHttpException("Failed to assign the ROLE to this User, please try again");
        }
        return ["status" => 200, 'Message' => "Role ADMIN assigned successfully"];
    }

    public function actionRolerevoker($userId, $userRole)
    {
        $auth = Yii::$app->authManager;
        // we first check if the user already has the role
        $isHe = $auth->getAssignment($userRole, $userId);
        if ($isHe) {
            $roler = $auth->getRole($userRole);
            if ($auth->revoke($roler, $userId)) {
                return ["status" => 200, 'message' => "Revoked / Denied this User the $userRole Role"];
            } else {
                throw new BadRequestHttpException("Failed to revoke $userRole from this User");
            }
        } else {
            throw new BadRequestHttpException("User Does not have Such a Role");
        }
    }
}


?>
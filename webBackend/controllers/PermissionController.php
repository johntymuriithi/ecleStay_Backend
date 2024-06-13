<?php

namespace app\controllers;

use app\models\Hosts;
use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;

class PermissionController extends BaseController
{
    public $modelClass = 'app\models\Services';

    public function actionAssignhost($userId)
    {
        $auth = Yii::$app->authManager;
//        $user = User::findOne(['id' => $userId]);
//        $email = $user->email;
//        $userHost = Hosts::findOne(['email' => $email]);
//        if (!$userHost) {
//            throw new BadRequestHttpException("Must os Sign Up with Us first");
//        }
        // we first check if the user already has the role
        $isHe = $auth->getAssignment('host', $userId);
        if ($isHe) {
            throw new BadRequestHttpException("User already has the ROLE");
        }
        $userRole = $auth->getRole('host');
        if (!$auth->assign($userRole, $userId)) {
            throw new BadRequestHttpException("Failed to assign the to this User, please try again");
        }

//        $userHost->approved = true;
//
//        $userr = new Hosts();
//        if (!$userHost->save()) {
//            throw new BadRequestHttpException("Failed to approve Hosts");
//        }
        return ["status" => 200, 'Message' => "Role HOST assigned successfully"];
    }
    public function actionAssignadmin($userId)
    {
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
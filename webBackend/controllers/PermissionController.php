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
        // Find the user by ID
        $user = User::findOne($userId);
        if (!$user) {
            throw new ForbiddenHttpException("User not found. Please sign up first.");
        }

        // Check if the user is registered as a host
        $email = $user->email;
        $host = Hosts::findOne(['email' => $email]);
        if (!$host) {
            throw new ForbiddenHttpException("Host record not found. Please register as a host.");
        }

        // Check if the user already has the 'host' role
        $auth = Yii::$app->authManager;
        if ($auth->getAssignment('host', $userId)) {
            throw new BadRequestHttpException("User already has the HOST role.");
        }

        // Assign the 'host' role to the user
        $hostRole = $auth->getRole('host');
        $auth->assign($hostRole, $userId);

        // Update the 'approved' status using direct SQL
        $updateCommand = Yii::$app->db->createCommand()
            ->update('hosts', ['approved' => true], ['email' => $email])
            ->execute();

        if ($updateCommand) {


            Yii::$app->mailer->compose()
                ->setFrom('ecleStay-no-reply@gmail.com')
                ->setTo($host->email)
                ->setSubject('Host Request Approval')
                ->setHtmlBody("<p>Hello {$host->host_name}, We are thrilled to inform you that you
 have been accepted to become our partner. You are Officially a HOST AT<h1>EcliStay</h1>. 
 Please Log in to our Web App to find your Dashboard</p>")

                ->send();
            return ['status' => 200, 'message' => 'Role HOST assigned and approved successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update host.'];
        }
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

        Yii::$app->mailer->compose()
            ->setFrom('ecleStay-no-reply@gmail.com')
            ->setTo($user->email)
            ->setSubject('Host Request Approval')
            ->setHtmlBody("<p>Hello {$user->host_name}, We are thrilled to inform you that you
 have been accepted to become our aDMIN. You are Officially a ADMIN AT<h1>EcliStay</h1>.</p>")
            ->send();
        return ["status" => 200, 'Message' => "Role ADMIN assigned successfully"];
    }

    public function actionRolerevoker($userId, $userRole)
    {
        $auth = Yii::$app->authManager;

        $user = User::findOne($userId);
        if (!$user) {
            throw new ForbiddenHttpException("User not found. Please sign up first.");
        }

        // Check if the user is registered as a host
        $email = $user->email;
        // we first check if the user already has the role
        $isHe = $auth->getAssignment($userRole, $userId);
        if ($isHe) {
            $roler = $auth->getRole($userRole);
            if ($auth->revoke($roler, $userId)) {
                $updateCommand = Yii::$app->db->createCommand()
                    ->update('hosts', ['approved' => false], ['email' => $email])
                    ->execute();


                Yii::$app->mailer->compose()
                    ->setFrom('ecleStay-no-reply@gmail.com')
                    ->setTo($user->email)
                    ->setSubject('Revocation of ' . $userRole . " Role")
                    ->setHtmlBody("<p>Hello , We are sorry to inform you that we have revoked your $userRole at<h1>EcliStay</h1>.
Please consider reaching to us for more Guidance. Thank you</p>")

                    ->send();
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
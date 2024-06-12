<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // Remove all existing roles and permissions
        $auth->removeAll();

        // Define roles
        $adminRole = $auth->createRole('admin');
        $hostRole = $auth->createRole('host');
        $userRole = $auth->createRole('user');

        // Add roles to authManager
        $auth->add($adminRole);
        $auth->add($hostRole);
        $auth->add($userRole);

        // Define permissions
        $createService = $auth->createPermission('createService');
        $updateService = $auth->createPermission('updateService');
        $deleteService = $auth->createPermission('deleteService');

        // Add permissions to authManager
        $auth->add($createService);
        $auth->add($updateService);
        $auth->add($deleteService);

        // Assign permissions to roles
        $auth->addChild($adminRole, $createService);
        $auth->addChild($adminRole, $updateService);
        $auth->addChild($adminRole, $deleteService);

        $auth->addChild($hostRole, $createService);
        $auth->addChild($hostRole, $updateService);

        // Assign roles to users
        $auth->assign($adminRole, 14); // Replace with actual user ID
        $auth->assign($hostRole, 14);  // Replace with actual user ID
        $auth->assign($userRole, 138);  // Replace with actual user ID
    }
}

<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // add "createPost" permission
        $createService = $auth->createPermission('uploadService');
        $createService->description = 'Upload services';
        $auth->add($createService);

        // add "author" role and give this role the "createPost" permission
        $miniAdmin = $auth->createRole('miniAdmin');
        $auth->add($miniAdmin);
        $auth->addChild($miniAdmin, $createService);

        // add "admin" role and give this role the "updatePost" permission
        // as well as the permissions of the "author" role
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $createService);

        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
        $auth->assign($miniAdmin, 2);
        $auth->assign($admin, 1);
    }
}
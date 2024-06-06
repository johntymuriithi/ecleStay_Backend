<?php

// rbac-init.php

// Include Yii bootstrap file
require(__DIR__ . '/path/to/yii.php');

// RBAC initialization code
use yii\rbac\DbManager;
use app\rbac\ServiceOwnerRule;
use app\models\User;

$auth = Yii::$app->authManager;

// Define roles
$adminRole = $auth->createRole('admin');
$hostRole = $auth->createRole('host');
$userRole = $auth->createRole('user');

// Save roles
$auth->add($adminRole);
$auth->add($hostRole);
$auth->add($userRole);

// Define permissions
$createService = $auth->createPermission('createService');
$updateService = $auth->createPermission('updateService');
$deleteService = $auth->createPermission('deleteService');

// Save permissions
$auth->add($createService);
$auth->add($updateService);
$auth->add($deleteService);

// Register custom rule
$auth->add(new ServiceOwnerRule());

echo "RBAC initialization completed.";

?>
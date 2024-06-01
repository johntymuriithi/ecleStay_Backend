<?php
namespace app\controllers;

use app\models\Roles;
use app\models\Types;
use Yii;
use yii\rest\ActiveController;
use app\models\County;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class RolesController extends BaseController
{
    public $modelClass = 'app\models\Roles'; // Specifies the model this controller will use

    public function actionShowroles()
    {
        $role = Roles::find()->all();
        if ($role) {

            return $role;

        } else {
            throw new NotFoundHttpException("No Roles in the database");
        }
    }

    public function actionAddrole()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = \Yii::$app->request->bodyParams;
        $role = new Roles();

        if ($role->load($params, '') && $role->save()) {
            return "Role Added Successfully";
        } else {
            throw new ForbiddenHttpException("You are very much Forbidden from this Action");
        }

    }
}

?>
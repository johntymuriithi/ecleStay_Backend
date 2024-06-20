<?php
namespace app\controllers;

use app\models\Types;
use Yii;
use yii\rest\ActiveController;
use app\models\County;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class TypesController extends BaseController
{
    public $modelClass = 'app\models\Types'; // Specifies the model this controller will use

    public function actionShowtypes()
    {
        $types = Types::find()->all();
        if ($types) {
            return $types;

        } else {
            throw new NotFoundHttpException("No types in the database");
        }
    }

    public function actionAddtypes()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = \Yii::$app->request->bodyParams;
        $type = new Types();

        if ($type->load($params, '') && $type->save()) {
            return "Type Added Successfully";
        } else {
            throw new ForbiddenHttpException("You are very much Forbidden from this Action");
        }

    }
}

?>
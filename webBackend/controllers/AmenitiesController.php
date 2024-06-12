<?php
namespace app\controllers;

use app\models\Amenities;
use yii\web\NotFoundHttpException;



class AmenitiesController extends BaseController
{
    public $modelClass = 'app\models\Amenities'; // Specifies the model this controller will use

    public function actionShowamenities()
    {
        $amenity = Amenities::find()->all();
        if ($amenity) {

            return $amenity;

        } else {
            throw new NotFoundHttpException("No Amenities in the database");
        }
    }

//    public function actionAddrole()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $params = \Yii::$app->request->bodyParams;
//        $role = new Roles();
//
//        if ($role->load($params, '') && $role->save()) {
//            return "Role Added Successfully";
//        } else {
//            throw new ForbiddenHttpException("You are very much Forbidden from this Action");
//        }
//
//    }
}

?>
<?php
namespace app\controllers;

use app\components\JwtAuth;
use Yii;
use yii\rest\ActiveController;
use app\models\County;
use yii\web\ForbiddenHttpException;
use yii\web\Response;


class CountyController extends BaseController
{
    public $modelClass = 'app\models\County'; // Specifies the model this controller will use

//    public function behaviors()
//    {
//        $behaviors = parent::behaviors();
//
//        // Add JWT authenticator
//        $behaviors['authenticator'] = [
//            'class' => JwtAuth::class,
//        ];
//
//        return $behaviors;
//    }

    public function actionShowcounties()
    {
//        if (Yii::$app->user->can('createService')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $counties = County::find()->all();
            return $counties;
//        } else {
//            throw new ForbiddenHttpException("You are restricted from this action / endpoint");
//        }
    }

    public function actionAddcounty()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = \Yii::$app->request->bodyParams;

        $county = new County();
        $county->county_name = $params['countyName'];
        $county->county_code = $params['countyCode'];
        $county->county_url = $params['countyUrl'];

        if ($county->save()) {
            return "County Added Successfully";
        } else {
            throw new ForbiddenHttpException("Well,,you are forbidden boohoo");
        }

    }
}

?>
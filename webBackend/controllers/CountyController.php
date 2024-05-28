<?php
namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\County;
use yii\web\ForbiddenHttpException;
use yii\web\Response;


class CountyController extends BaseController
{
    public $modelClass = 'app\models\County'; // Specifies the model this controller will use

    public function actionShowcounties()
    {
        $counties = County::find()->all();
        return $counties;
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
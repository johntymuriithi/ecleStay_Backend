<?php
namespace app\controllers;

use app\models\Hosts;
use app\models\Types;
use Yii;
use yii\rest\ActiveController;
use app\models\County;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class HostsController extends BaseController
{
    public $modelClass = 'app\models\Hosts'; // Specifies the model this controller will use

    public function actionShowhosts()
    {
        $hosts = Hosts::find()->all();
        if ($hosts) {

            return $hosts;

        } else {
            throw new NotFoundHttpException("No Hosts in the database");
        }
    }

    public function actionAddhosts()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = \Yii::$app->request->bodyParams;

        $host = new Hosts();
        $host->host_name = $params['host_name'];
        $host->language = $params['language'];
        $host->email = $params['email'];
        $host->about = $params['about'];
        $host->picture = $params['host_picture'];
        $host->number = $params['number'];
        $host->county_id = $params['location'];

        if ($host->save()) {
            return "Hosts Added Successfully";
        } else {
            print_r($host->getErrors());
//            throw new ForbiddenHttpException("Well,,you are forbidden boohoo");
        }

    }
}

?>
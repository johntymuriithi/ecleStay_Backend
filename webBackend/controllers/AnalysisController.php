<?php

namespace app\controllers;

use app\models\Hosts;
use app\models\Services;
use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AnalysisController extends BaseController
{
    public $modelClass = 'app\models\Services';

    public function actionHostii()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $host = Yii::$app->user->id;

        $user = User::findOne(['id' => $host]);
        if (!$user) {
            throw new ForbiddenHttpException("Please Sign Up first to proceed with this Request");
        }
        $email = $user->email;
        $userHost = Hosts::findOne(['email' => $email]);
        $host_id = $userHost->host_id;

//        var_dump(Services::findOne(['host_id' => $host_id]));exit;

        if (Services::findOne(['host_id' => $host_id])) {
            $services = Services::find()
                ->with('images', 'county', 'hosts', 'roles', 'amenities')
                ->where(['host_id' => $host_id])
                ->asArray() // Convert the result to an array
                ->one();

            $services = array($services); // wow,,I can do it yoh!!!!

            // Check if the service was found

            foreach ($services as &$service) {
                if (isset($service['images']) && is_array($service['images'])) {
                    foreach ($service['images'] as &$image) {
//                   $image['service_image'] = '/var/www/html/ecleStay_Backend/webBackend/' . $image['service_image'];
                        $image['service_image'] =Yii::$app->params['imageLink'] . '/' . $image['service_image'];
                    }
                }
                if (isset($service['hosts']) && is_array($service['hosts'])) {
                    $service['hosts']['picture'] = Yii::$app->params['imageLink'] . '/' . $service['hosts']['picture'];
                    $service['hosts']['business_doc'] = '/var/www/html/ecleStay_Backend/webBackend/' . $service['hosts']['business_doc'];
                    $service['hosts']['hostReviews'] = Yii::$app->runAction('hoster/hostreviews', ['id' => $service['hosts']['host_id']]);

                }
                if (isset($service['county']) && is_array($service['county'])) {
                    $service['county']['county_url'] = Yii::$app->params['imageLink'] . '/' . $service['county']['county_url'];
                }

                $service['serviceReviews'] = Yii::$app->runAction('servicer/servicereviews', ['id' => $service['service_id']]);
            }

                return [
                    'status' => 200,
                    'hostName' =>  $userHost->host_name,
                    "numberServices" => count($services),
                    'Services' => $services,
                ];
        } else {
            throw new BadRequestHttpException("HOST has No added services yet");
        }
    }

    public function actionTester()
    {
        return "Bra Bra";
    }
}

?>
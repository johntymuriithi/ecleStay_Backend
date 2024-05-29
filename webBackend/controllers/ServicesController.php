<?php
namespace app\controllers;

use app\models\Images;
use app\models\Services;
use app\models\Types;
use Yii;
use yii\rest\ActiveController;
use app\models\County;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class ServicesController extends BaseController
{
    public $modelClass = 'app\models\Services'; // Specifies the model this controller will use

    public function actionShowservices()
    {
        $service = Services::find()->all();
        if ($service) {

            return $service;

        } else {
            throw new NotFoundHttpException("No Hosts in the database");
        }
    }

    public function actionAddservice()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction = Yii::$app->db->beginTransaction(); // Start transaction
        $params = \Yii::$app->request->bodyParams;

       try {
           $service = new Services();
           $service->price = $params['price'];
           $service->pricing_criteria = $params['pricing_criteria'];
           $service->description = $params['description'];
           $service->type_id = $params['type_id'];
           $service->host_id = $params['host_id'];
           $service->start_date = $params['start_date'];
           $service->county_id = $params['county_id'];
           $service->end_date = $params['end_date'];

           if ($service->save()) {
               foreach ($params['pictures'] as $picture) {
                   $images = new Images();
                   $images->service_id = $service->service_id;
                   $images->image_url = $picture;

                   if (!$images->save()) {
                       // If saving picture fails, throw an exception
                       throw new \Exception('Failed to save service picture');
                   }
               }
               $transaction->commit();
               return ['status' => 'success', 'message' => 'Service Added Successfully'];
           } else {
               // If saving service fails, throw an exception
               throw new \Exception('Failed to save service');
           }
       } catch (\Exception $e) {
           // Roll back the transaction in case of an error
           $transaction->rollBack();
           throw new ForbiddenHttpException($e->getMessage());
       }

    }
}

?>
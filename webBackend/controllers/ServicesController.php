<?php
namespace app\controllers;

use app\models\Images;
use app\models\Services;
use app\models\Types;
use Yii;
use yii\rest\ActiveController;
use app\models\County;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class ServicesController extends BaseController
{
    public $modelClass = 'app\models\Services'; // Specifies the model this controller will use

    public function actionAddservice()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction = Yii::$app->db->beginTransaction(); // Start transaction
        $params = \Yii::$app->request->bodyParams;

        try {
            $service = new Services();

            if ($service->load($params, '') && $service->save()) {
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

    public function actionGetservices() {
        $services = Services::find()
            ->with('images', 'county', 'hosts')
            ->asArray() // Convert the result to an array
            ->all();
        return $services;
    }

    public function actionViewservice($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Find the service by ID, including related images and county
        $service = Services::find()
            ->with('images', 'county', 'hosts', 'roles')
            ->where(['service_id' => $id])
            ->asArray() // Convert the result to an array
            ->one();

        // Check if the service was found
        if ($service) {
            // Return the service data
            return $service;
        } else {
            // Throw an exception if the service was not found
            throw new NotFoundHttpException("Service not found");
        }
    }
}

?>
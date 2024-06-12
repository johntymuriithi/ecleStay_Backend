<?php
namespace app\controllers;

use app\models\Amenities;
use app\models\Images;
use app\models\Roles;
use app\models\Services;
use app\models\Types;
use Yii;
use yii\rest\ActiveController;
use app\models\County;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;


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

            // add service images
            if ($service->load($params, '') && $service->save()) {
                $model = new Images();
                $model->imageFiles = UploadedFile::getInstancesByName('imageFiles');
                if (Yii::$app->request->isPost && $model->validate()) {
                    foreach ($model->imageFiles as $file) {
                        $relativePath = 'uploads/' . uniqid() . '.' . $file->extension;
                        $absolutePath = Yii::getAlias('@app/') . $relativePath;

                        if ($file->saveAs($absolutePath)) {
                            $id = $service->service_id;
//                            $id = $_POST['service_id'];
                            if (!Images::createImage($id, $relativePath)) {
                                throw new BadRequestHttpException("Failed to Upload Service Image to the db");
                            }
                        } else {
                            Yii::error("Failed to save the file to: " . $absolutePath);
                            throw  new ForbiddenHttpException("Failed to save th File to Uploads");
                        }
                    }
//                    return ['status' => 200, 'message' => 'Service Images Saved Successfully to the database'];

                } else {
                    var_dump("Validation failed: " . json_encode($model->getErrors()));
                    throw new BadRequestHttpException("Validation of Service Images Failed, Please try again later");
                }
                // add extra amenities
                foreach ($_POST['amenities'] as $amenity) {
                    $amenities = new Amenities();
                    $amenities->service_id = $service->service_id;
                    $amenities->amenity_name = $amenity;

                    if (!$amenities->save()) {
                        // If saving picture fails, throw an exception
                        throw new \Exception('Failed to save service Amenities');
                    }
                }
                // add service roles over here
                foreach ($_POST['roles'] as $role) {
                    $roles = new Roles();
                    $roles->service_id = $service->service_id;
                    $roles->role_name = $role;

                    if (!$roles->save()) {
                        // If saving picture fails, throw an exception
                        throw new \Exception('Failed to save service Roles');
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
            ->with('images', 'county', 'hosts', 'roles', 'amenities')
            ->asArray() // Convert the result to an array
            ->all();
        if ($services) {
            return [
                'status' => 200,
            'data' => ['Services' => $services],
                'message' => 'Services Retrived Successfully',
            ];
        } else {
            throw new NotFoundHttpException("No services Found in the Database");
        }
    }
    public function actionViewservice($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Find the service by ID, including related images and county
        $service = Services::find()
            ->with('images', 'county', 'hosts', 'roles', 'amenities')
            ->where(['service_id' => $id])
            ->asArray() // Convert the result to an array
            ->one();

        // Check if the service was found
        if ($service) {
            return [
                'status' => 200,
                'data' => ['Service' => $service],
                'message' => 'Service Retrived Successfully',
            ];
        } else {
            // Throw an exception if the service was not found
            throw new NotFoundHttpException("Service not found");
        }
    }
}

?>
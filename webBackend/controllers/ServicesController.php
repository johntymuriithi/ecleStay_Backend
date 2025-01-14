<?php
namespace app\controllers;

use app\models\Amenities;
use app\models\Images;
use app\models\Roles;
use app\models\Services;
use app\models\Types;
use DateTime;
use yii\helpers\Url;
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
    public $modelClass = 'app\models\User';// Specifies the model this controller will use

    public function actionAddservice()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction = Yii::$app->db->beginTransaction(); // Start transaction
        $params = \Yii::$app->request->bodyParams;

        try {
            $service = new Services();

            // add service images

//            return [$service->load($params, '') && $service->save(), $service->getErrors(), $params];
            if ($service->load($params, '') && $service->save()) {
                $model = new Images();
                $model->imageFiles = UploadedFile::getInstancesByName('imageFiles');
//                if (!$model->validate()) {
//                    var_dump($model->imageFiles);
//                    var_dump($model->getErrors());
//                }
//                echo 'yo';
//                var_dump($model->validate());exit;
//                return [Yii::$app->request->isPost && $model->validate(), $model->getErrors(), $model->imageFiles, $params];exit;
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
//                    var_dump("Validation failed: " . json_encode($model->getErrors()));
//                    return ["message" => "Here", $model->imageFiles];
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
                Yii::error("Service failed to save. Validation errors: " . json_encode($service->getErrors()), __METHOD__);
                throw new \Exception('Failed to save service yo');
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

        foreach ($services as &$service) {

            if (isset($service['images']) && is_array($service['images'])) {
                $service['county_id'] = $this->helperCounty($service['county_id']);
                foreach ($service['images'] as &$image) {
//                    $image['service_image'] = '/var/www/html/ecleStay_Backend/webBackend/' . $image['service_image'];
                    $baseUrl = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl;
                    $image['service_image'] = Yii::$app->params['imageLink'] . '/' . $image['service_image'];
                }
            }
            if (isset($service['hosts']) && is_array($service['hosts'])) {
                $service['hosts']['picture'] =  Yii::$app->params['imageLink'] . '/'. $service['hosts']['picture'];
                $service['hosts']['business_doc'] = '/var/www/html/ecleStay_Backend/webBackend/' . $service['hosts']['business_doc'];
                $service['hosts']['created_at'] = $this->helperDate( $service['hosts']['created_at']);
                $service['hosts']['hostReviews'] = Yii::$app->runAction('hoster/hostreviews', ['id' => $service['hosts']['host_id']]);
                $service['hosts']['county_id'] = $this->helperCounty($service['hosts']['county_id']);


            }
            if (isset($service['county']) && is_array($service['county'])) {
                $service['county']['county_url'] = Yii::$app->params['imageLink'] . '/' . $service['county']['county_url'];
//                $service['county']['county_id'] = $this->helperCounty($service['county']['county_id']);
            }

            $service['serviceReviews'] = Yii::$app->runAction('servicer/servicereviews', ['id' => $service['service_id']]);

        }
        if ($services) {
            return [
                'status' => 200,
                'message' => 'Services Retrived Successfully',
                'data' => ['Services' => $services],
            ];
        } else {
            throw new NotFoundHttpException("No services Found in the Database");
        }
    }
    public function actionViewservice($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Services::findOne(['service_id' => $id]))
            throw new NotFoundHttpException("The Service was not Found");
        // Find the service by ID, including related images and county
        $services = Services::find()
            ->with('images', 'county', 'hosts', 'roles', 'amenities')
            ->where(['service_id' => $id])
            ->asArray() // Convert the result to an array
            ->one();

        $services = array($services); // wow,,I can do it yoh!!!!

        // Check if the service was found

        foreach ($services as &$service) {
            $service['county_id'] = $this->helperCounty($service['county_id']);
            if (isset($service['images']) && is_array($service['images'])) {
                foreach ($service['images'] as &$image) {
//                   $image['service_image'] = '/var/www/html/ecleStay_Backend/webBackend/' . $image['service_image'];
                    $image['service_image'] =Yii::$app->params['imageLink'] . '/' . $image['service_image'];
                }
            }
            if (isset($service['hosts']) && is_array($service['hosts'])) {
                $service['hosts']['picture'] = Yii::$app->params['imageLink'] . '/' . $service['hosts']['picture'];
                $service['hosts']['business_doc'] = '/var/www/html/ecleStay_Backend/webBackend/' . $service['hosts']['business_doc'];
                $service['hosts']['created_at'] = $this->helperDate( $service['hosts']['created_at']);
                $service['hosts']['hostReviews'] = Yii::$app->runAction('hoster/hostreviews', ['id' => $service['hosts']['host_id']]);
                $service['hosts']['county_id'] = $this->helperCounty($service['hosts']['county_id']);

            }
            if (isset($service['county']) && is_array($service['county'])) {
                $service['county']['county_url'] = Yii::$app->params['imageLink'] . '/' . $service['county']['county_url'];
//                $service['county']['county_id'] = $this->helperCounty($service['county']['county_id']);
            }

            $service['serviceReviews'] = Yii::$app->runAction('servicer/servicereviews', ['id' => $service['service_id']]);
        }

        if ($services) {
            return [
                'status' => 200,
                'message' => 'Service Retrived Successfully',
                'data' => ['Service' => $services],
            ];
        } else {
            // Throw an exception if the service was not found
            throw new NotFoundHttpException("Service not found");
        }
    }

    public function actionSearchcounty($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Services::findOne(['county_id' => $id]))
            throw new NotFoundHttpException("The Service was not Found");
        // Find the service by ID, including related images and county
        $services = Services::find()
            ->with('images', 'county', 'hosts', 'roles', 'amenities')
            ->where(['county_id' => $id])
            ->asArray() // Convert the result to an array
            ->one();

        $services = array($services); // wow,,I can do it yoh!!!!

        // Check if the service was found

        foreach ($services as &$service) {
            $service['county_id'] = $this->helperCounty($service['county_id']);
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
                $service['hosts']['county_id'] = $this->helperCounty($service['hosts']['county_id']);

            }
            if (isset($service['county']) && is_array($service['county'])) {
                $service['county']['county_url'] = Yii::$app->params['imageLink'] . '/' . $service['county']['county_url'];
//                $service['county']['county_id'] = $this->helperCounty($service['county']['county_id']);
            }

            $service['serviceReviews'] = Yii::$app->runAction('servicer/servicereviews', ['id' => $service['service_id']]);
        }

        if ($services) {
            return [
                'status' => 200,
                'message' => 'Service Retrived Successfully',
                'data' => ['Service' => $services],
            ];
        } else {
            // Throw an exception if the service was not found
            throw new NotFoundHttpException("County not found");
        }
    }

    private function helperDate($timestamp)
    {
        // Get the current date and time
        $currentDate = new DateTime();

        // Convert the timestamp to DateTime object
        $registrationDate = new DateTime(Yii::$app->formatter->asDate($timestamp, 'php:Y-m-d H:i:s'));

        // Calculate the difference
        $interval = $currentDate->diff($registrationDate);

        // Format the difference
        if ($interval->y > 0) {
            return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' of hosting';
        } elseif ($interval->m > 0) {
            return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' of hosting';
        } else {
            return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' of hosting';
        }
    }

    public function helperCounty($id) {
        $record = County::findOne(['county_id' => $id]);
        return $record->county_name;
    }






}

?>
<?php

namespace app\controllers;

use app\models\County;
use app\models\Guides;
use app\models\Hosts;
use app\models\Orders;
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

    public function actionHostiorders()
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
            $services = Orders::find()
//                ->with('images', 'orders')
                ->where(['host_id' => $host_id])
                ->asArray() // Convert the result to an array
                ->all();

//            $services = array($services); // wow,,I can do it yoh!!!!

            // Check if the service was found


            foreach ($services as &$service) {
                $service['GuestDetails'] = $this->findUser($service['user_id']);
            }
            unset($service); // Break the reference with the last element

            return [
                'status' => 200,
                'hostName' =>  $userHost->host_name,
                "numberOrders" => count($services),
                'Orders' => $services,
            ];
        } else {
            throw new BadRequestHttpException("HOST has No added services yet");
        }
    }

    public function actionServiceorders($serviceId)
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
            $services = Orders::find()
//                ->with('images', 'orders')
                ->where(['service_id' => $serviceId])
                ->asArray() // Convert the result to an array
                ->all();

//            $services = array($services); // wow,,I can do it yoh!!!!

            // Check if the service was found


            foreach ($services as &$service) {
                $service['GuestDetails'] = $this->findUser($service['user_id']);
            }
            unset($service); // Break the reference with the last element

            return [
                'status' => 200,
                'hostName' =>  $userHost->host_name,
                "numberOrders" => count($services),
                'Orders' => $services,
            ];
        } else {
            throw new BadRequestHttpException("HOST has No added services yet");
        }
    }

    public function actionApprovedhosts()
    {
        $total = [];
        $hosts = Hosts::findAll(['approved' => 'true']);

        if ($hosts) {
            foreach ($hosts as $host) {
                $user = User::findOne(['email' => $host->email]);
                $reviewData = [
                    'host_id' => $host->host_id,
                    'user_id' => $user->id,
                    'host_name' => $host->host_name,
                    'roles' => $this->helper($user->id)
                ];

                $total[] = $reviewData;
            }
        } else {
            throw new NotFoundHttpException("No approved Hosts Found, Try again later");
        }

        return ["status" => 200, "message" => "Approved Host retrived succcesifully", "hosts" => $total];
    }

    public function actionWaitinghosts()
    {
        $total = [];
        $hosts = Hosts::findAll(['approved' => 'false']);

        if ($hosts) {
            foreach ($hosts as $host) {
                $user = User::findOne(['email' => $host->email]);
                $reviewData = [
                    'host_id' => $host->host_id,
                    'user_id' => $user->id,
                    'host_name' => $host->host_name,
                ];

                $total[] = $reviewData;
            }
        } else {
            throw new NotFoundHttpException("Every Hosts has been Approved. Thank you");
        }

        return ["status" => 200, "message" => "Waiting for Approval Host retrived succcesifully", "hosts" => $total];
    }

    public function actionGuidebycounty($countyId) {
        $guides = Guides::find()->where(['county_id' => $countyId])->andWhere(['approved' => 'true'])->all();
        $totalGuides = [];

        if(!$guides) {
            throw new NotFoundHttpException("Guide on that county not available");
        }

        foreach ($guides as $guide) {
            $gudi = [
                "guide_id" => $guide->guide_id,
                'county_id' => $this->helperCounty($guide->county_id),
                "about" => $guide->about,
                "guide_name" => $guide->guide_name,
                "language" => $guide->language,
                "email" => $guide->email,
                "number" => $guide->number,
                "picture" => Yii::$app->params['imageLink'] . '/' . $guide->picture,
            ];

            $totalGuides = $gudi;
        }

        return [$totalGuides];



    }

    public function actionApprovedguides()
    {
        $total = [];
        $guides = Guides::findAll(['approved' => 'true']);

        if ($guides) {
            foreach ($guides as $guide) {
                $user = User::findOne(['email' => $guide->email]);
                $reviewData = [
                    'guide_id' => $guide->guide_id,
                    'user_id' => $user->id,
                    'guide_name' => $guide->guide_name,
                    'roles' => $this->helper($user->id)
                ];

                $total[] = $reviewData;
            }
        } else {
            throw new NotFoundHttpException("No approved Guides Found, Try again later");
        }

        return ["status" => 200, "message" => "Approved guides retrived succcesifully", "Guides" => $total];
    }

    public function actionWaitingguides()
    {
        $total = [];
        $guides = Guides::findAll(['approved' => 'false']);

        if ($guides) {
            foreach ($guides as $guide) {
                $user = User::findOne(['email' => $guide->email]);
                $reviewData = [
                    'guide_id' => $guide->guide_id,
                    'user_id' => $user->id,
                    'guide_name' => $guide->guide_name,
                    'roles' => $this->helper($user->id)
                ];

                $total[] = $reviewData;
            }
        } else {
            throw new NotFoundHttpException("Every Guides has been Approved. Thank you");
        }

        return ["status" => 200, "message" => "Waiting for Approval Guides retrived succcesifully", "Guides" => $total];
    }

    public function helper ($id) {
        // Get the authManager component
        $roles = Yii::$app->authManager->getRolesByUser($id);
        return array_keys($roles);
    }

    public function findUser($id)
    {
        $user = User::findOne(['id' => $id]);
        $totalReviews = [];
        if ($user) {
            $reviewData = [
                'userPic' => $user->profilePic ?? null,
                'userName' => $user->first_name . ' ' . $user->second_name,
                'userNumber' => $user->phone,
                'userEmail' => $user->email,
            ];
        }

        return $reviewData;
    }

    public function helperCounty($id) {
        $record = County::findOne(['county_id' => $id]);
        return $record->county_name;
    }
}

?>
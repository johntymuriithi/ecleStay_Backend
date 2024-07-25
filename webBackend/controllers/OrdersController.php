<?php
namespace app\controllers;

use app\models\Orders;
use app\models\Services;
use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\NotFoundHttpException;


class OrdersController extends BaseController
{
    public $modelClass = 'app\models\Orders'; // Specifies the model this controller will use

    public function actionShoworders()
    {
        $orders = Orders::find()->all();
        if ($orders) {
            return $orders;
        } else {
            throw new NotFoundHttpException("No Orders for now!");
        }
    }

    // lets now handle ordering man man

    public function actionOrdernow() {

        $id = Yii::$app->user->id;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $params = \Yii::$app->request->bodyParams;

        $orders = new Orders();
        $orders->user_id = $id;

        if ($orders->load($params, '') && $orders->save()) {
            return ["status" => 200 . " " . 'OK', 'message' => "Order placed Successfully"];
        } else {
            throw new BadRequestHttpException("Order placement Failed");
        }
    }

    public function actionUserguest() {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $users = User::find()
            ->innerJoinWith('orders')
            ->all();

        $total = [];
        if ($users) {
            foreach ($users as $user) {
                $reviewData = [
                    'host_id' => $user->id,
                    'user_name' => $user->first_name . " " . $user->second_name,
                    "email" => $user->email,
                    'roles' => $this->helper($user->id)
                ];

                $total[] = $reviewData;
            }
        } else {
            throw new NotFoundHttpException("No Users  Found, Try again later");
        }

        return ["status" => 200, "message" => "Active Users Guests retrived succcesifully", "totalUsers" => count($total), "users" => $total];
    }

    public function helper ($id) {
        // Get the authManager component
        $roles = Yii::$app->authManager->getRolesByUser($id);
        return array_keys($roles);
    }

    public function actionGuestservices() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->user->id;

        $userServices = (new \yii\db\Query())
            ->select(['service_id'])
            ->from('orders')
            ->where(['user_id' => $id])
            ->andwhere(['paid' => 'true'])
            ->column();

        if (!$userServices) {
            throw new NotFoundHttpException("User has no Bookings made yet");
        }

        $totalServices = [];
        foreach ($userServices as $service) {
            $services = Services::find()
            ->with('images', 'county', 'hosts', 'roles', 'amenities')
            ->where(['service_id' => $service])
            ->asArray() // Convert the result to an array
            ->one();

            array_push($totalServices, $services);
        }

//        return $totalServices;
//        if (!Services::findOne(['county_id' => $id]))
//            throw new NotFoundHttpException("The Service was not Found");
//        // Find the service by ID, including related images and county
//        $services = Services::find()
//            ->with('images', 'county', 'hosts', 'roles', 'amenities')
//            ->where(['county_id' => $id])
//            ->asArray() // Convert the result to an array
//            ->one();
//
//        $services = array($services); // wow,,I can do it yoh!!!!
//
//        // Check if the service was found

        $services = $totalServices;
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
                'message' => 'Your Bookings Retrived Successfully',
                'data' => ['Bookings' => $services],
            ];
    }

}

?>
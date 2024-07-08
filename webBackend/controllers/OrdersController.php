<?php
namespace app\controllers;

use app\models\Orders;
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

}

?>
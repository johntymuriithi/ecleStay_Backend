<?php
namespace app\controllers;

use app\models\Orders;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\NotFoundHttpException;


class OrdersController extends BaseController
{
    public $modelClass = 'app\models\Roles'; // Specifies the model this controller will use

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
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $params = \Yii::$app->request->bodyParams;

        $orders = new Orders();
        if ($orders->load($params, '') && $orders->save()) {
            return ["status" => 200 . " " . 'OK', 'message' => "Order placed Successfully"];
        } else {
            throw new ForbiddenHttpException("Order placement failed");
        }
    }

}

?>
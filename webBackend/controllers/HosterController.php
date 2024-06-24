<?php
namespace app\controllers;

use app\models\Hoster;
use app\models\Hosts;
use app\models\Orders;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class HosterController extends BaseController {

    // action to review a Host
    // but the guest must have paid/ booked the hotel / so check if actually he is in the orders table and has paid = true;
    public $modelClass = 'app\models\Hoster';

    public function actionReviewhost()
    {
        $userId = Yii::$app->user->id;
        $params = \Yii::$app->request->bodyParams;
        $host_id = $params['host_id'];
        $service_id = $params['service_id'];// this from param

        // check if actually this hosts is available in the hosts table
        $host = Hosts::findOne(['host_id' => $host_id]);
        if (!$host)
            throw new ForbiddenHttpException("There is No hosts that you want to review for");
        $user = Orders::find()
            ->where(['user_id' => $userId])
            ->andWhere(['service_id' => $service_id])
            ->andWhere(['paid' => 'true'])
            ->one();
        if (!$user)
            throw new ForbiddenHttpException("Book or Pay First to be able to review"); // meaning has not stayed

        $hoster = Hoster::find()
            ->where(['host_id' => $host_id])
            ->andWhere(['user_id' => $userId])
            ->one();
        if ($hoster) {
            throw  new ForbiddenHttpException("Already reviewed this Host, please Edit your review Only Or leave it alone");
        }

        $review = new Hoster();
        $review->user_id = $userId;
        if ($review->load($params, '') && $review->save()) {
            return ["status" => 200, "message" => "Host Review Was Successful"];
        } else {
            throw new BadRequestHttpException("Failed to save Review, please try again");
        }
    }
}














?>
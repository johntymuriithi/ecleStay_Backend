<?php
namespace app\controllers;

use app\models\Hoster;
use app\models\Hosts;
use app\models\Orders;
use app\models\User;
use Cassandra\Date;
use DateTime;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

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

    public function actionHostreviews($id)
    {
        $limit = 20; // Limit to fetch
        $page = Yii::$app->request->get('page', 1); // Get the current page from request
        $offset = ($page - 1) * $limit;

        $reviews = Hoster::find()->where(['host_id' => $id])->limit($limit)->offset($offset)->all();
        if (!$reviews)
            return [];
        $totalReviews = [];

        $averageRating = (int)Hoster::find()->average('rating');

        foreach ($reviews as $review) {
            $user = User::findOne(['id' => $review->user_id]);

            if ($user) {
                $reviewData = [
                    'review_id' => $review->hoster_id,
                    'review_date' => $this->helperDate($review->review_date),
                    'content' => $review->description,
                    'rating' => $review->rating,
                    'userPic' => $user->profilePic ?? null,
                    'userName' => $user->first_name . ' ' . $user->second_name,
                    'user_registerDate' => $this->helperDate($user->created_at),
                ];

                $totalReviews[] = $reviewData;
            } else {
                throw new NotFoundHttpException("User with ID {$review->user_id} not found.");
            }
        }

        return ['averageRating' => $averageRating, 'totalReviews' => count($totalReviews), "reviews" => $totalReviews];
    }

    private function helperDate($timestamp)
    {
        // Convert and format the timestamp
        return Yii::$app->formatter->asDate($timestamp, 'php:j F Y');
    }


//    public function helperDate($user) {
//        $date = new DateTime($user->created_at);
//        $formattedDate = $date->format('j F Y');
//        return $formattedDate;
//    }
}














?>
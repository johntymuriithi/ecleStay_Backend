<?php
namespace app\controllers;

use app\models\Hoster;
use app\models\Hosts;
use app\models\Orders;
use app\models\Servicer;
use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ServicerController extends BaseController {

    // action to review a Host
    // but the guest must have paid/ booked the hotel / so check if actually he is in the orders table and has paid = true;
    public $modelClass = 'app\models\Servicer';

    public function actionReviewservice()
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

        // this stops review more than once to either service / host
        $servicer = Servicer::find()
            ->where(['service_id' => $service_id])
            ->andWhere(['user_id' => $userId])
            ->one();
        if ($servicer) {
            throw  new ForbiddenHttpException("Already reviewed this Service, please Edit your review Only Or leave it alone");
        }

        $review = new Servicer();
        $review->user_id = $userId;
        if ($review->load($params, '') && $review->save()) {
            return ["status" => 200, "message" => "Service Review Was Successful"];
        } else {
            var_dump($review->getErrors());
            throw new BadRequestHttpException("Failed to save Review, please try again");
        }
    }

    public function actionServicereviews($id)
    {
        $limit = 20; // Limit to fetch
        $page = Yii::$app->request->get('page', 1); // Get the current page from request
        $offset = ($page - 1) * $limit;

        $reviews = Servicer::find()->where(['service_id' => $id])->limit($limit)->offset($offset)->all();
        if (!$reviews)
            return [];

        $totalReviews = [];
        $averageRating = (int)Servicer::find()->average('rating');
        $averageCleanliness = (int)Servicer::find()->average('cleanliness');
        $averageLocation = (int)Servicer::find()->average('location');
        $averageCommunication = (int)Servicer::find()->average('communication');


        foreach ($reviews as $review) {
            $user = User::findOne(['id' => $review->user_id]);

            if ($user) {
                if ($user->profilePic) {
                    $pic = Yii::$app->params['imageLink'] . '/'. $user->profilePic;
                } else {
                    $pic = null;
                }
                $reviewData = [
                    'review_id' => $review->servicer_id,
                    'review_date' => $this->helperDate($review->review_date),
                    'content' => $review->description,
                    'rating' => $review->rating,
                    'userPic' => $pic ?? null,
                    'userEmail' => $user->email,
                    'userName' => $user->first_name . ' ' . $user->second_name,
                    'daysStayed' => $review->days_stayed,
                    'user_registerDate' => $this->helperDate($user->created_at),
                ];

                $totalReviews[] = $reviewData;
            } else {
                throw new NotFoundHttpException("User with ID {$review->user_id} not found.");
            }
        }

        return ["averageRating" => $averageRating, "averageCleanliness" => $averageCleanliness,
            "averageLocation" => $averageLocation, "averageCommunication" => $averageCommunication,
            "totalReviews" => count($totalReviews), "reviews" => $totalReviews];
    }

    private function helperDate($timestamp)
    {
        // Convert and format the timestamp
        return Yii::$app->formatter->asDate($timestamp, 'php:j F Y');
    }
}














?>
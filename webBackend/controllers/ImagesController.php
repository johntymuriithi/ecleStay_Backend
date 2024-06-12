<?php
namespace app\controllers;

use app\models\Images;
use app\models\Orders;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;


class ImagesController extends BaseController
{
    public $modelClass = 'app\models\Images'; // Specifies the model this controller will use
    public function actionUploadimage()
    {
        $model = new Images();

        $model->imageFiles = UploadedFile::getInstancesByName('imageFiles');
        if (Yii::$app->request->isPost && $model->validate()) {
            foreach ($model->imageFiles as $file) {
                $relativePath = 'uploads/' . uniqid() . '.' . $file->extension;
                $absolutePath = Yii::getAlias('@app/') . $relativePath;

                if ($file->saveAs($absolutePath)) {
                    $id = $_POST['service_id'];
                    if (!Images::createImage($id, $relativePath)) {
                        throw new BadRequestHttpException("Failed to Upload Service Image to the db");
                    }
                } else {
                    Yii::error("Failed to save the file to: " . $absolutePath);
                    throw  new ForbiddenHttpException("Failed to save th File to Uploads");
                }
            }
             return ['status' => 200, 'message' => 'Service Images Saved Successfully to the database'];

        } else {
            var_dump("Validation failed: " . json_encode($model->getErrors()));
            throw new BadRequestHttpException("Validation of Service Images Failed, Please try again later");
        }
    }



}

?>
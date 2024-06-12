<?php
namespace app\controllers;

use app\models\Hosts;
use app\models\Types;
use Yii;
use yii\rest\ActiveController;
use app\models\County;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;


class HostsController extends BaseController
{
    public $modelClass = 'app\models\Hosts'; // Specifies the model this controller will use

    public function actionShowhosts()
    {
        $hosts = Hosts::find()->all();
        if ($hosts) {
            return $hosts;
        } else {
            throw new NotFoundHttpException("No Hosts in the database");
        }
    }

    public function actionAddhosts()
    {
        // Initialize new Hosts model
        $host = new Hosts();

        // Assign POST data to the model attributes
        $host->host_name = Yii::$app->request->post('host_name');
        $host->business_name = Yii::$app->request->post('business_name');
        $host->business_doc = Yii::$app->request->post('business_doc');
        $host->language = Yii::$app->request->post('language');
        $host->email = Yii::$app->request->post('email');
        $host->about = Yii::$app->request->post('about');
        $host->number = Yii::$app->request->post('number');
        $host->county_id = Yii::$app->request->post('location');

        // Get uploaded file
        $host->imageFile = UploadedFile::getInstanceByName('imageFile');
//        $host->businessFile = UploadedFile::getInstanceByName('businessFile');


        // Check if request is POST and validate model
        if (Yii::$app->request->isPost && $host->validate()) {
            // Define uploads directory path
            $uploadsDir = Yii::getAlias('@app/web/uploads/hosts');
            if (!is_dir($uploadsDir)) {
                // Create the directory if it does not exist
                if (!mkdir($uploadsDir, 0755, true)) {
                    Yii::error("Failed to create directory: " . $uploadsDir);
                    throw new \yii\web\ServerErrorHttpException("Failed to create directory: " . $uploadsDir);
                }
            }

            // Generate a unique file name
            $uniqueFileName = uniqid() . '.' . $host->imageFile->extension;

            // Define relative and absolute paths
            $relativePath = 'uploads/hosts/' . $uniqueFileName;
            $absolutePath = Yii::getAlias('@app/web/') . $relativePath;

            // Save the uploaded file
            if ($host->imageFile->saveAs($absolutePath)) {
                // Attempt to insert data into the database
                if (Hosts::hostImager($relativePath, Yii::$app->request->post())) {
                    return "Hosts Added Successfully";
                } else {
                    Yii::error("Failed to insert data into database.");
                    throw new \yii\web\BadRequestHttpException("Failed to insert data into database.");
                }
            } else {
                Yii::error("Failed to save the file to: " . $absolutePath);
                throw new \yii\web\ServerErrorHttpException("Failed to save the file to: " . $absolutePath);
            }
        } else {
            // Output validation errors
            Yii::error("Validation failed: " . json_encode($host->getErrors()));
            return "Validation failed: " . json_encode($host->getErrors());
        }
    }

}

?>
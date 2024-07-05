<?php
namespace app\controllers;

use app\models\Hosts;
use app\models\Types;
use app\models\User;
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
            foreach ($hosts as &$image) {
                $image['picture'] = '/var/www/html/ecleStay_Backend/webBackend/' . $image['picture'];
                $image['business_doc'] = '/var/www/html/ecleStay_Backend/webBackend/' . $image['business_doc']; // don't do in productions Jonhty
            }
            return $hosts;
        } else
            throw new NotFoundHttpException("No Hosts in the database");
    }

    public function actionAddhosts()
    {
        // Initialize new Hosts model
        $host = new Hosts();

        // Assign POST data to the model attributes
        $host->host_name = Yii::$app->request->post('host_name');
        $host->business_name = Yii::$app->request->post('business_name');
        $host->language = Yii::$app->request->post('language');
        $host->email = Yii::$app->request->post('email');
        $host->about = Yii::$app->request->post('about');
        $host->number = Yii::$app->request->post('number');
        $host->county_id = Yii::$app->request->post('location');

        $user = User::findOne(['email' => $host->email]);
        if (!$user) {
            throw new ForbiddenHttpException("Please Sign Up first with this email > $host->email < to Access this Action");
        }
        $user = $host::findOne(['email' => $host->email]);
        if ($user) {
            throw new ForbiddenHttpException("Hosts with that Email already Exists");
        }
        $user = $host::findOne(['number' => $host->number]);
        if ($user) {
            throw new ForbiddenHttpException("Hosts with that Number already Exists");
        }
        // Get uploaded file
        $host->imageFile = UploadedFile::getInstanceByName('imageFile');
        $host->businessFile = UploadedFile::getInstanceByName('businessFile');

        // Check if request is POST and validate model
        if (Yii::$app->request->isPost && $host->validate()) {
            // Define uploads directory path
            $uploadsDir = Yii::getAlias('@app/uploads/hosts');
            $uploadsDir2 = Yii::getAlias('@app/uploads/files');
            if (!is_dir($uploadsDir)) {
                if (!mkdir($uploadsDir, 0755, true)) {
                    Yii::error("Failed to create directory: " . $uploadsDir);
                    throw new \yii\web\ServerErrorHttpException("Failed to create directory: " . $uploadsDir);
                }
            }
            if (!is_dir($uploadsDir2)) {
                if (!mkdir($uploadsDir2, 0755, true)) {
                    Yii::error("Failed to create directory: " . $uploadsDir2);
                    throw new \yii\web\ServerErrorHttpException("Failed to create directory: " . $uploadsDir2);
                }
            }
            $uniqueFileName = uniqid() . '.' . $host->imageFile->extension;
            $relativePath = 'uploads/hosts/' . $uniqueFileName;
            $absolutePath = Yii::getAlias('@app/') . $relativePath;
            $uniqueFileName1 = uniqid() . '.' . $host->businessFile->extension;
            $relativePath1 = 'uploads/files/' . $uniqueFileName1;
            $absolutePath1 = Yii::getAlias('@app/') . $relativePath1;

            // Save the uploaded file
            if ($host->imageFile->saveAs($absolutePath) && $host->businessFile->saveAs($absolutePath1)) {
                if (Hosts::hostImager($relativePath, Yii::$app->request->post(), $relativePath1)) {
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
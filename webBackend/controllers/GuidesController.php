<?php
namespace app\controllers;

use app\models\Guides;
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


class GuidesController extends BaseController
{
    public $modelClass = 'app\models\Hosts'; // Specifies the model this controller will use

    public function actionShowguides()
    {
        $guides= Guides::find()->all();
        if ($guides) {
            foreach ($guides as &$image) {
                $image['picture'] = Yii::$app->params['imageLink'] . '/'. $image['picture'];
                $image['business_doc'] = '/var/www/html/ecleStay_Backend/webBackend/' . $image['business_doc']; // don't do in productions Jonhty
            }
            return $guides;
        } else
            throw new NotFoundHttpException("No Guides in the database");
    }

    public function actionAddguides()
    {
        // Initialize new Hosts model
        $guide = new Guides();

        // Assign POST data to the model attributes
        $guide->guide_name = Yii::$app->request->post('guide_name');
        $guide->language = Yii::$app->request->post('language');
        $guide->email = Yii::$app->request->post('email');
        $guide->about = Yii::$app->request->post('about');
        $guide->number = Yii::$app->request->post('number');
        $guide->county_id = Yii::$app->request->post('location');

        $user = User::findOne(['email' => $guide->email]);
        if (!$user) {
            throw new ForbiddenHttpException("Please Sign Up first with this email > $guide->email < to Access this Action");
        }
        $user = $guide::findOne(['email' => $guide->email]);
        if ($user) {
            throw new ForbiddenHttpException("Guide with that Email already Exists");
        }
        $user = $guide::findOne(['number' => $guide->number]);
        if ($user) {
            throw new ForbiddenHttpException("Guide with that Number already Exists");
        }
        // Get uploaded file
        $guide->imageFile = UploadedFile::getInstanceByName('imageFile');
        $guide->businessFile = UploadedFile::getInstanceByName('businessFile');

        // Check if request is POST and validate model
        if (Yii::$app->request->isPost && $guide->validate()) {
            // Define uploads directory path
            $uploadsDir = Yii::getAlias('@app/uploads/guides');
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
            $uniqueFileName = uniqid() . '.' . $guide->imageFile->extension;
            $relativePath = 'uploads/guides/' . $uniqueFileName;
            $absolutePath = Yii::getAlias('@app/') . $relativePath;
            $uniqueFileName1 = uniqid() . '.' . $guide->businessFile->extension;
            $relativePath1 = 'uploads/files/' . $uniqueFileName1;
            $absolutePath1 = Yii::getAlias('@app/') . $relativePath1;

            // Save the uploaded file
            if ($guide->imageFile->saveAs($absolutePath) && $guide->businessFile->saveAs($absolutePath1)) {
                if (Guides::hostImager($relativePath, Yii::$app->request->post(), $relativePath1)) {
                    return "Guide Added Successfully";
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
            Yii::error("Validation failed: " . json_encode($guide->getErrors()));
            return "Validation failed: " . json_encode($guide->getErrors());
        }
    }

}

?>
<?php
namespace app\controllers;

use app\components\JwtAuth;
use app\models\Hosts;
use Yii;
use yii\rest\ActiveController;
use app\models\County;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;


class CountyController extends BaseController
{
    public $modelClass = 'app\models\County'; // Specifies the model this controller will us

    public function actionShowcounties()
    {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $counties = County::find()->all();

            if ($counties) {
                foreach ($counties as &$image) {
                    $image['county_url'] = '/var/www/html/ecleStay_Backend/webBackend/' . $image['county_url'];
                }
                return $counties;
            } else {
                throw new NotFoundHttpException("No counties in the moment");
            }
    }

    public function actionAddcounty()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = \Yii::$app->request->bodyParams;

        $county = new County();
        $county->county_name = Yii::$app->request->post('county_name');
        $county->county_code = Yii::$app->request->post('county_code');

        $county->countyFile = UploadedFile::getInstanceByName('countyFile');
        // Check if request is POST and validate model
        if (Yii::$app->request->isPost && $county->validate()) {
            // Define uploads directory path
            $uploadsDir = Yii::getAlias('@app/uploads/county');
            if (!is_dir($uploadsDir)) {
                if (!mkdir($uploadsDir, 0755, true)) {
                    Yii::error("Failed to create directory: " . $uploadsDir);
                    throw new \yii\web\ServerErrorHttpException("Failed to create directory: " . $uploadsDir);
                }
            }
            $uniqueFileName = uniqid() . '.' . $county->countyFile->extension;
            $relativePath = 'uploads/county/' . $uniqueFileName;
            $absolutePath = Yii::getAlias('@app/') . $relativePath;

            // Save the uploaded file
            if ($county->countyFile->saveAs($absolutePath)) {
                if (County::countyImager($relativePath, Yii::$app->request->post())) {
                    return "County Added Successfully";
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
            Yii::error("Validation failed: " . json_encode($county->getErrors()));
            return "Validation failed: " . json_encode($county->getErrors());
        }

//        if ($county->save()) {
//            return "County Added Successfully";
//        } else {
//            throw new ForbiddenHttpException("Well,,you are forbidden boohoo");
//        }
    }
}

?>
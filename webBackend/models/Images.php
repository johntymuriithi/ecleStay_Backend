<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class Images extends ActiveRecord
{
   public $imageFiles;// Public property to store file instances
//    public $file_path;

    public static function tableName()
    {
        return 'serviceImages';
    }

    public function rules()
    {
        return [
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 4, 'message' => 'Please upload a file.'],
            [['service_image'], 'string'], // Assuming file_path is stored as a string
        ];
//        return [
//            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 4, 'checkExtensionByMimeType' => false, 'message' => 'Please upload a file.'],
//        ];
//        return [
//            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 4],
//            [['service_image'], 'string', 'max' => 200], // Ensure file_path is expected as a string
//            [['service_id'], 'integer'], // If you have a service_id, ensure it is validated as an integer
//        ];
    }

    public static function createImage($serviceId, $imageUrl)
    {
        return Yii::$app->db->createCommand()->insert(self::tableName(), [
            'service_image' => $imageUrl,
            'service_id' => $serviceId,
        ])->execute();
    }


}

?>

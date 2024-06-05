<?php
namespace app\controllers;

use app\models\Categories;

use app\models\Hosts;
use app\models\Services;
use app\models\Types;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class CategoriesController extends BaseController
{
    public $modelClass = 'app\models\Categories'; // Specifies the model this controller will use

    // getting categories over here
    public function actionShowcategories()
    {
        $category = Categories::find()->all();
        if ($category) {

            return $category;

        } else {
            throw new NotFoundHttpException("No types in the database");
        }
    }

    // adding categories over here
    public function actionAddcategory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = \Yii::$app->request->bodyParams;

        $category = new Categories();
        $category->category_name = $params['category_name'];

        if ($category->save()) {
            return ["status" => "OK", "message" => "Category Added Successfully"];
        } else {
            throw new ForbiddenHttpException("You are very much Forbidden from this Action");
        }

    }

    // get accommodations

    public function actionGetaccommodations($categoryName) {
        // Find the category by name
        Yii::$app->response->format = Response::FORMAT_JSON;

        $category = Categories::find()->where(['category_name' => $categoryName])->one();
        if (!$category) {
            throw new NotFoundHttpException("Category not found");
        }

        // Find services belonging to the category
        $services = Services::find()
            ->joinWith(['types' => function ($query) use ($category) {
                $query
                    ->where(['types.category' => $category->category_id]);
            }])
            ->with(['hosts'])
            ->all();
        return $services;
//        $response = [];

//        foreach ($services as $service) {
//            $response[] = [
//                'service_id' => $service->service_id,
//                'host_id' => $service->host_id,
//                'price' => $service->price,
//                'pricing_criteria' => $service->pricing_criteria,
//                'description' => $service->description,
//                'type_id' => $service->type_id,
//                'county_id' => $service->county_id,
//                'start_date' => $service->start_date,
//                'end_date' => $service->end_date,
//                'approved' => $service->approved,
//                'images' => $service->images,
//                'county' => $service->county,
//                'hosts' => $service->hosts,
//                'types' => $service->types,
//            ];
//        }
//
//        return $response;
    }

    // search By type
    public function actionSearchtype($type) {
        $row = Types::findOne(['type_name' => $type]);
        if ($row) {
            $services = Services::find()->where(['type_id' => $row->type_id])->all();
            $service = Services::find()
                ->with('images', 'hosts', 'county')
                ->where(['type_id' => $row->type_id])
                ->asArray()
                ->all();
            if ($service) {
                return $service;
            } else {
                throw new NotFoundHttpException("Service not found");
            }
        } else {
            throw new NotFoundHttpException("Data does not exists,,search another thing");
        }
    }
}

?>
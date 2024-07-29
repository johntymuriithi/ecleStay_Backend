<?php
namespace app\controllers;

use app\models\Categories;

use app\models\County;
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
            throw new NotFoundHttpException("No categories in the database");
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
                $query->where(['types.category' => $category->category_id]);
            }])
            ->with(['hosts', 'county', 'images']) // Eager load related models
            ->all();
        if ($services) {
            foreach ($services as &$service) {
                $service['county_id'] = $this->helperCounty($service['county_id']);
                if (isset($service['images']) && is_array($service['images'])) {
                    $test = $service['images'];
                    foreach ($test as &$image) {
                        $image['service_image'] = Yii::$app->params['imageLink'] . '/' . $image['service_image'];
                    }
                }
                if (isset($service['hosts'])) {
                    // Ensure the base URL is prepended only once for picture
                    if (strpos($service['hosts']['picture'], Yii::$app->params['imageLink']) === false) {
                        $service['hosts']['picture'] = Yii::$app->params['imageLink'] . '/' . $service['hosts']['picture'];
                        $service['hosts']['county_id'] = $this->helperCounty($service['hosts']['county_id']);
                    }

                    $service['hosts']['business_doc'] = [];
                    $service['hosts']['business_name'] = [];
                }
                if (isset($service['county'])) {
                    // Ensure the base URL is prepended only once for county_url
                    if (strpos($service['county']['county_url'], Yii::$app->params['imageLink']) === false) {
                        $service['county']['county_url'] = Yii::$app->params['imageLink'] . '/' . $service['county']['county_url'];
                    }
                }
            }
            return [
                'status' => 200,
                'message' => 'Services By Category Retrieved Successfully',
                'data' => array_map(function($service) {
                    $serviceData = $service->toArray([], ['serviceReviews']); // Request extra field 'serviceReviews'
                    $hostData = $service->hosts->toArray([], ['hostReviews']); // Include extra fields for host
                    $serviceData['hosts'] = $hostData;
                    return $serviceData;
                }, $services),
            ];
        } else {
            throw new NotFoundHttpException("Services of the category $categoryName not found");
        }

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

    public function helperCounty($id) {
        $record = County::findOne(['county_id' => $id]);
        return $record->county_name;
    }
}

?>
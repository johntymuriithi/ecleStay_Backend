<?php
namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\User;
use yii\web\Response;
use yii\web\BadRequestHttpException;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionSignup()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;

        $requiredFields = ['first_name', 'second_name', 'email', 'phone', 'password'];

        foreach ($requiredFields as $field) {
            if (!isset($params[$field])) {
                return ['status' => false, 'message' => "Field '$field' is required"];
            }
        }

        $user = new User();
        $user->first_name = $params['first_name'];
        $user->second_name = $params['second_name'];
        $user->email = $params['email'];
        $user->phone = $params['phone'];
        $user->setPassword($params['password']);
        $user->generateAuthKey();

        if ($user->save()) {
            return ['status' => true, 'message' => 'User created successfully'];
        } else {
            return ['status' => false, 'errors' => $user->errors];
        }
    }

    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $user = User::findOne(['email' => $params['email']]);

    if ($user && Yii::$app->security->validatePassword($params['password'], $user->password_hash)) {
        return ['status' => true, 'message' => 'Login successful', 'user' => $user];
    } else {
        throw new BadRequestHttpException('Invalid email or password');
    }
    }
}

?>
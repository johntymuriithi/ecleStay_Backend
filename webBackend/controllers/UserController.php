<?php
namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\User;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User'; // specifies the model this controller will use

    public function behaviors() {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    // restrict access to
                    'Origin' => (YII_ENV_PROD) ? [''] : ['http://localhost:5173'], '*', // look at this
                    // Allow only POST and PUT methods
                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
                    // Allow only headers 'X-Wsse'
                    'Access-Control-Request-Headers' => ['X-Wsse', 'Content-Type'],
                    // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                    'Access-Control-Allow-Credentials' => true,
                    // Allow OPTIONS caching
                    'Access-Control-Max-Age' => 3600,
                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
                ],
            ],
        ];
    }
    public function actions() // modifies the default actions defined by the ActiveController class
    {
        $actions = parent::actions(); // gets the default actions
        unset($actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionSignup()
    {
        Yii::$app->response->format = Response::FORMAT_JSON; // sets the response to be inform of JSON format
        $params = Yii::$app->request->bodyParams; // retrives the request body params the params passed

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

        $availableUser = User::findOne(['email' => $params['email']]);
        $phone = User::findOne(['phone' => $params['phone']]);
//        var_dump($availableUser);
        if ($user->save()) {
            // send email here
            Yii::$app->mailer->compose('welcome', ['user' => $user])
                ->setFrom('no-reply@example.com')
                ->setTo($user->email)
                ->setSubject('Welcome to our application')
                ->send();

            return ['status' => true, 'message' => 'User created successfully'];
        } elseif ($availableUser != null) {
            return ['status' => 409, 'message' => 'Email already exist ', 'user' => $user];
        }elseif ($phone != null) {
            return ['status' => 409, 'message' => 'phone already exist ', 'user' => $user];
        } else{
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
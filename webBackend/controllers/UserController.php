<?php
namespace app\controllers;

use Cassandra\Value;
use Yii;
use yii\base\Security;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use app\models\User;
use yii\web\ConflictHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use app\models\PasswordResetToken;
use yii\filters;
//use app\components\JwtMiddleware;
//use app\components\BaseController;

class UserController extends BaseController
{
    public $modelClass = 'app\models\User'; // specifies the model this controller will use

//    public function behaviors()
//    {
//        $behaviors = parent::behaviors();
//
//        // Enable JSON output
//        $behaviors['contentNegotiator'] = [
//            'class' => \yii\filters\ContentNegotiator::class,
//            'formats' => [
//                'application/json' => Response::FORMAT_JSON,
//            ],
//        ];
//
//        // Add CORS filter
//        $behaviors['corsFilter'] = [
//            'class' => \yii\filters\Cors::className(),
//            'cors' => [
//                'Origin' => (YII_ENV_PROD) ? [''] : ['http://localhost:5177', 'https://fe59-41-90-101-26.ngrok-free.app', '*'],
//                'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
//                'Access-Control-Request-Headers' => ['X-Wsse', 'Content-Type', '*'],
//                'Access-Control-Allow-Credentials' => true,
//                'Access-Control-Max-Age' => 3600,
//                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
//            ],
//        ];
//
//        return $behaviors;
//    }
//
//    public function actions()
//    {
//        $actions = parent::actions();
//        // Disable default actions if needed
//        unset($actions['create'], $actions['update'], $actions['delete'], $actions['view']);
//        return $actions;
//    }
//    public function behaviors() {
//
//        return [
//            'corsFilter' => [
//                'class' => \yii\filters\Cors::className(),
//                'cors' => [
//                    // restrict access to
//                    'Origin' => (YII_ENV_PROD) ? [''] : ['http://localhost:5177'], ['https://fe59-41-90-101-26.ngrok-free.app/'], '*', // look at this
//                    // Allow only POST and PUT methods
//                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
//                    // Allow only headers 'X-Wsse'
//                    'Access-Control-Request-Headers' => ['X-Wsse', 'Content-Type'], '*',
//                    // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
//                    'Access-Control-Allow-Credentials' => true,
//                    // Allow OPTIONS caching
//                    'Access-Control-Max-Age' => 3600,
//                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
//                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
//                ],
//            ],
//        ];
//    }
    // i have the CROS origin issues here
//    public function behaviors() {
//        $behaviors = parent::behaviors();
//
//        $behaviors['jwtAuth'] = [
//            'class' => JwtMiddleware::className(),
//            'except' => ['login', 'signup', 'resetpasswordlink'], // Exclude login and signup actions from JWT auth
//        ];

//        $behaviors['corsFilter'] = [
//            'class' => \yii\filters\Cors::className(),
//            'cors' => [
//                // restrict access to
//                'Origin' => (YII_ENV_PROD) ? [''] : ['http://localhost:5173'], '*', // look at this
//                // Allow only POST and PUT methods
//                'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
//                // Allow only headers 'X-Wsse'
//                'Access-Control-Request-Headers' => ['X-Wsse', 'Content-Type'], '*',
//                // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
//                'Access-Control-Allow-Credentials' => true,
//                // Allow OPTIONS caching
//                'Access-Control-Max-Age' => 3600,
//                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
//                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
//            ],
//        ];

//        $behaviors['access'] = [
//            'class' => AccessControl::className(),
//            'denyCallback' => function ($rule, $action) {
//                throw new \Exception('You are not allowed to access this page');
//            },
//            'only' => ['resetpasswordlin'],
//            'rules' => [
//                [
//                    'allow' => true,
//                    'actions' => ['login', 'signup', 'resetpasswordlink'],
//                    'roles' => ['?'], // Guest users
//                ],
//                // You can add more rules here to control access to other actions
//            ],
//        ];

//        return $behaviors;

//        return [
//            'corsFilter' => [
//                'class' => \yii\filters\Cors::className(),
//                'cors' => [
//                    // restrict access to
//                    'Origin' => (YII_ENV_PROD) ? [''] : ['http://localhost:5174'], '*', // look at this
//                    // Allow only POST and PUT methods
//                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
//                    // Allow only headers 'X-Wsse'
//                    'Access-Control-Request-Headers' => ['X-Wsse', 'Content-Type'], '*',
//                    // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
//                    'Access-Control-Allow-Credentials' => true,
//                    // Allow OPTIONS caching
//                    'Access-Control-Max-Age' => 3600,
//                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
//                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
//                ],
//            ],
//            'access' => [
//                'class' => AccessControl::className(),
////                'denyCallback' => function ($rule, $action) {
////                    throw new \Exception('You are not allowed to access this page');
////                },
//                'only' => ['resetpasswordlink'],
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'actions' => ['signup', 'resetpasswordlink', 'login'],
//                        'roles' => ['?'],
//                    ],
//                ]
//            ]
//        ];
//    }
//    public function actions() // modifies the default actions defined by the ActiveController class
//    {
//        $actions = parent::actions(); // gets the default actions
//        unset($actions['create'], $actions['update'], $actions['delete']);
//        return $actions;
//    }

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
        $user->generateActivationToken();

        $availableUser = User::findOne(['email' => $params['email']]);
        $phone = User::findOne(['phone' => $params['phone']]);
        if ($user->save()) {
            $tokenJWT = $user->generateJwt();
            Yii::$app->mailer->compose()
                ->setFrom('ecleStay-no-reply@gmail.com')
                ->setTo($user->email)
                ->setSubject('Welcome to ecleStay')
                ->setHtmlBody("<p>Welcome {$user->first_name} {$user->second_name}, to <h1>EcliStay</h1></p>
<p>Please click below Button to activate your account</p>
<a href='https://e3b9-41-90-101-26.ngrok-free.app/user/activateuser?token={$user->activationToken}'>Activate Account</a>")
                ->send();
            return ['status' => 200];
        } elseif ($availableUser != null) {
            throw new ConflictHttpException("User with the same Email Exists");
        }elseif ($phone != null) {
            throw new ConflictHttpException("User with the same Number Exists");
        } else{
            return ['status' => false, 'errors' => $user->errors];
        }
    }
    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $user = User::findOne(['email' => $params['email']]);
        if (!$user) {
            throw new NotFoundHttpException("User does not exists");
        } else {
            if ($user->userActive === true) {
                if ($user && Yii::$app->security->validatePassword($params['password'], $user->password_hash)) {
                    $tokenJWTs = $user->generateJwt();
                    return ['status' => 200, 'token' => $tokenJWTs];
                } else {
                    throw new BadRequestHttpException('Invalid email or password');
                }
            } else {
                throw new UnauthorizedHttpException("Please Active your account using the email sent to YOU!");
            }
        }
    }

    public function actionResetpasswordlink() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $args = Yii::$app->request->bodyParams;
        $user = User::findOne(['email' => $args['email']]);

        if ($user) {
            $userId = $user->id;
            $token = Yii::$app->security->generateRandomString(64);
            $expireDate = time() + 200000;
            if (PasswordResetToken::createToken($userId, $token, $expireDate)) {
                // Send the token to the user via email
                $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['user/resetpassword', 'token' => $token]);
                Yii::$app->mailer->compose()
                    ->setFrom('ecleStay-password-reset@gmail.com')
                    ->setTo($user->email)
                    ->setSubject('Password Reset')
                    ->setHtmlBody("<p>Hello {$user->first_name},</p><p>We have recieved a reqeust for a reset of password.
Click link below change your password <h1>Reset Link:</h1><i><a href='{$resetLink}'>Reset Password Here</a></i>
<p>Please do ignore this Link if you didn't request it, Thank You</p>")
                    ->send();

                return ["Status" => '200 OK'];
            } else {
                throw new \RuntimeException('Failed to create password reset token.');
            }
        } else {
            throw new UnauthorizedHttpException("User with the email does not exists");
        }
    }

    public function actionResetpassword($token)
    {
        var_dump($token);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userParams = Yii::$app->request->bodyParams;
        $hashedTokenFromUser = hash('sha256', $token);
        $user = User::findOne(['email' => $userParams['email']]);
        $userId = $user->id;
        $userRows = PasswordResetToken::findAll(['user_id' => $userId]);

        foreach ($userRows as $idToken) {
            if ($idToken->token === $hashedTokenFromUser && $idToken->token_expiry < time()) {
                throw new BadRequestHttpException("Link has expired");
            }
        }
        $newPassword = $userParams['password'];
        $newPasswordHashed = Yii::$app->security->generatePasswordHash($newPassword);
        $user->password_hash = $newPasswordHashed;
        if ($user->save()) {
            foreach ($userRows as $row) {
                $row->delete();
            }
//            return $this->redirect('https://fe59-41-90-101-26.ngrok-free.app'); // direct to login page
        } else {
            throw new NotAcceptableHttpException("Not acceptable,, Sorry");
        }
        return ["Do it again"];
    }

    // don'$this->do thos in production please this is for testing only
    public function actionToa()
    {
        $users = User::find()->all();
        foreach ($users as $user) {

            $user->delete();
        }
    }

    public function actionActivateuser($token)
    {
        $user = User::findOne(['activationToken' => $token]);

        if ($user && !$user->userActive) {
            $user->userActive = true;
            $user->activationToken = null; // Clear the token after activation
            if ($user->save()) {
                // Redirect to frontend login page
                return "Activated successfully, Waiting for frontend guys now";
//                return $this->redirect('https://fe59-41-90-101-26.ngrok-free.app');
            } else {
                throw new BadRequestHttpException("Activation Failed");
            }
        } else {
            throw new BadRequestHttpException("Invalid or Expired Token");
        }
    }

}
?>
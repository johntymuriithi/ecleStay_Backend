<?php
namespace app\controllers;

use Cassandra\Value;
use Yii;
use yii\base\Security;
use yii\rest\ActiveController;
use app\models\User;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use app\models\PasswordResetToken;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User'; // specifies the model this controller will use

    // i have the CROS origin issues here
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
                    'Access-Control-Request-Headers' => ['X-Wsse', 'Content-Type'], '*',
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


        $availableUser = User::findOne(['email' => $params['email']]);
        $phone = User::findOne(['phone' => $params['phone']]);
        if ($user->save()) {
            $tokenJWT = $user->generateJwt();
            Yii::$app->mailer->compose()
                ->setFrom('ecleStay-no-reply@gmail.com')
                ->setTo($user->email)
                ->setSubject('Welcome to ecleStay')
                ->setHtmlBody("<p>Hello {$user->first_name},</p><p>Welcome EcliStay</p><p>Thank you for signing up.</p>")
                ->send();
            return ['status' => 200, 'message' => $tokenJWT];
        } elseif ($availableUser != null) {
            return ['status' => 401, 'message' => 'Email already exist '];
        }elseif ($phone != null) {
            return ['status' => 405, 'message' => 'phone already exist '];
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
        return ['status' => 200, 'message' => 'Login successful', 'user' => $user];
    } else {
        throw new BadRequestHttpException('Invalid email or password');
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
Click link below change your password <h1>Reset Link:</h1><i>$resetLink</i>
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
            return ["status" => 200 . "" . " OK"];
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


    // generate your token staff here
//    public function GenerateFunction($userId, $user) {
        // nothing
//        $token = Security::generateRandomString(64);
//        $expireDate = time() + 600;
//
//        // Insert the token into the database using the model method
//        if (PasswordResetToken::createToken($userId, $token, $expireDate)) {
//            // Send the token to the user via email
//            $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $token]);
//
//            Yii::$app->mailer->compose()
//                ->setTo($user->email)
//                ->setSubject('Password Reset Request')
//                ->setTextBody("Please click the following link to reset your password: $resetLink")
//                ->send();
//
////            return $this->render('requestPasswordResetSuccess');
//        } else {
//            throw new \RuntimeException('Failed to create password reset token.');
//        }
//    }
}
?>
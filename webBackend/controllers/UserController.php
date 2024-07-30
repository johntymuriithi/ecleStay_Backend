<?php
namespace app\controllers;

use app\models\County;
use app\models\Hosts;
use app\models\Orders;
use app\models\Services;
use Cassandra\Value;
use PHPUnit\Framework\Constraint\Count;
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
use yii\web\UploadedFile;

//use app\components\JwtMiddleware;
//use app\components\BaseController;

class UserController extends BaseController
{
    public $modelClass = 'app\models\User';// specifies the model this controller will use


    public function actionSignup()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $user = new User();
        $user->first_name = Yii::$app->request->post('first_name');
        $user->second_name = Yii::$app->request->post('second_name');
        $user->email = Yii::$app->request->post('email');
        $user->phone = Yii::$app->request->post('phone');

        $requiredFields = ['first_name', 'second_name', 'email', 'phone', 'password'];

        foreach ($requiredFields as $field) {
            if (!isset($params[$field])) {
                return ['status' => false, 'message' => "Field '$field' is required"];
            }
        }
        if (User::findOne(['email' => $params['email']])) {
            throw new ConflictHttpException("User with the same Email Exists");
        }
        if (User::findOne(['phone' => $params['phone']])) {
            throw new ConflictHttpException("User with the same Number Exists");
        }

        // from here incase of profile pic// but am not gonna use this for varius reasons
//        if (UploadedFile::getInstanceByName('imageFile')) {
//            $user->imageFile = UploadedFile::getInstanceByName('imageFile');
//            if (Yii::$app->request->isPost && $user->validate()) {
//
//                $uploadsDir = Yii::getAlias('@app/uploads/users');
//                if (!is_dir($uploadsDir)) {
//                    if (!mkdir($uploadsDir, 0755, true)) {
//                        Yii::error("Failed to create directory: " . $uploadsDir);
//                        throw new \yii\web\ServerErrorHttpException("Failed to create directory: " . $uploadsDir);
//                    }
//                }
//                $uniqueFileName = uniqid() . '.' . $user->imageFile->extension;
//                $relativePath = 'uploads/users/' . $uniqueFileName;
//                $absolutePath = Yii::getAlias('@app/') . $relativePath;
//
//                if ($user->imageFile->saveAs($absolutePath)) {
//                   // outaaa here
//
//                    $availableUser = User::findOne(['email' => $params['email']]);
//                    $phone = User::findOne(['phone' => $params['phone']]);
//                    if (User::userImager($relativePath, Yii::$app->request->post())) {
//                        // here oooo
//                        $auth = Yii::$app->authManager;
//                        $userRole = $auth->getRole('user');
//                        $id = self::getSome($params['email']);
//                        $auth->assign($userRole, $id);
//
//                        Yii::$app->mailer->compose()
//                            ->setFrom('ecleStay-no-reply@gmail.com')
//                            ->setTo($user->email)
//                            ->setSubject('Welcome to ecleStay')
//                            ->setHtmlBody("<p>Welcome {$user->first_name} {$user->second_name}, to <h1>EcliStay</h1></p>
//<p>Please click below Button to activate your account</p>
//<a href='https://a145-41-90-101-26.ngrok-free.app/user/activateuser?token={$user->activationToken}'>Activate Account</a>")
//                            ->send();
//                        return ['status' => 200, 'message' => 'User Sign up was Successful'];
//                    } elseif ($availableUser != null) {
//                        throw new ConflictHttpException("User with the same Email Exists");
//                    }elseif ($phone != null) {
//                        throw new ConflictHttpException("User with the same Number Exists");
//                    } else {
//                        return ['status' => false, 'errors' => $user->errors];
//                    }
//
//                    // to here also
//                } else {
//                    Yii::error("Failed to save the file to: " . $absolutePath);
//                    throw new \yii\web\ServerErrorHttpException("Failed to save the file to: " . $absolutePath);
//                }
//            } else {
//                // Output validation errors
//                Yii::error("Validation failed: " . json_encode($user->getErrors()));
//                return "Validation failed: " . json_encode($user->getErrors());
//            }
//        }

        // to here

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
            // here oooo
            $auth = Yii::$app->authManager;
            $userRole = $auth->getRole('user');
            $auth->assign($userRole, $user->id);

            $user->blocked = false;
            $user->login_trials = 0;
            $user->save();
//            $tokenJWT = $user->generateJwt(); // do it during login
            Yii::$app->mailer->compose()
                ->setFrom('ecleStay-no-reply@gmail.com')
                ->setTo($user->email)
                ->setSubject('Welcome to ecleStay')
                ->setHtmlBody("<p>Welcome {$user->first_name} {$user->second_name}, to <h1>EcliStay</h1></p>
<p>Please click below Button to activate your account</p>
<a href='https://d6a6-41-80-114-128.ngrok-free.app/user/activateuser?token={$user->activationToken}'>Activate Account</a>")
                ->send();
            return ['status' => 200, 'message' => 'User Sign up was Successful'];
        } elseif ($availableUser != null) {
            throw new ConflictHttpException("User with the same Email Exists");
        }elseif ($phone != null) {
            throw new ConflictHttpException("User with the same Number Exists");
        } else {
            return ['status' => false, 'errors' => $user->errors];
        }
    }

    // helper for signup, doing some staffy here
    public static function getSome($email) {
        $user = User::findOne(['email' => $email]);
        return $user['id'];
    }

    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $user = User::findOne(['email' => $params['email']]);
        if (!$user) {
            throw new NotFoundHttpException("User does not exists");
        } else {
            if (!$user->blocked){
                if ($user->userActive === true) {
                    if ($user && Yii::$app->security->validatePassword($params['password'], $user->password_hash)) {
                        $tokenJWTs = $user->generateJwt();
                        // give the user role here
                        $user->blocked = false;
                        $user->login_trials = 0;
                        $user->save();

                        if ($user->profilePic) {
                            $pic = Yii::$app->params['imageLink'] . '/'. $user->profilePic;
                        } else {
                            $pic = null;
                        }
                        return ['status' => 200, 'data' => ['token' => $tokenJWTs, 'profiles' => ['profilePicture' => $pic]], 'Message' => 'Logged in Successfully'];
                    } else {
                        $trials = $user->login_trials;
                        $user->login_trials = $trials + 1;
                        $user->save();

                        if ($user->login_trials >= 3) {
                            $user->blocked = true;
                            $user->save();
                            throw new ForbiddenHttpException("User Blocked");
                        }
                        throw new BadRequestHttpException('Invalid password');
                    }
                } else {
                    throw new UnauthorizedHttpException("Please Active your account using the email sent to YOU!");
                }
            } else {
                Yii::$app->response->statusCode = 403;
                return [
                    'status' => 403,
                    'message' => 'User is temporarily blocked, please reset your password to continue',
                ];
                // frontend to redirect to reset password thingy
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
            $expireDate = time() + 12000000;
            if (PasswordResetToken::createToken($userId, $token, $expireDate)) {
                // Send the token to the user via email
                $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['reset', 'token' => $token]);
                Yii::$app->mailer->compose()
                    ->setFrom('ecleStay-password-reset@gmail.com')
                    ->setTo($user->email)
                    ->setSubject('Password Reset')
                    ->setHtmlBody("<p>Hello {$user->first_name},</p><p>We have recieved a reqeust for a reset of password.
Click link below change your password <h1>Reset Link:</h1><i><a href='{$resetLink}'>Reset Password Here</a></i>
<p>Please do ignore this Link if you didn't request it, Thank You</p>")
                    ->send();

                return ["Status" => '200 OK', "resentLink" => $resetLink, "token" => $token];
            } else {
                throw new \RuntimeException('Failed to create password reset token.');
            }
        } else {
            throw new UnauthorizedHttpException("User with the email does not exists");
        }
    }

    public function actionResetpassword($token)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userParams = Yii::$app->request->bodyParams;
        $hashedTokenFromUser = hash('sha256', $token);
        $user = User::findOne(['email' => $userParams['email']]);
        if (!$user) {
            throw new NotFoundHttpException("Email user does not exist");
        }
        $userId = $user->id;
        $userRows = PasswordResetToken::findAll(['user_id' => $userId]);
        $validToken = null;
        foreach ($userRows as $idToken) {
            if ($idToken->token === $hashedTokenFromUser && $idToken->token_expiry > time()) {
                $validToken = $idToken;
                break;
            }
        }
        if ($validToken) {
            $newPassword = $userParams['password'];
            $newPasswordHashed = Yii::$app->security->generatePasswordHash($newPassword);
            $user->password_hash = $newPasswordHashed;

            if ($user->blocked === true) {
                $user->blocked = false;
                $user->login_trials = 0;
                $user->save();
            }
            if ($user->save()) {
                foreach ($userRows as $row) {
                    $row->delete();
                }
                return ["status" => 200, "message" => "Reset Successful, Continue with $newPassword as your password"];
            } else {
                throw new NotAcceptableHttpException("Not acceptable, Sorry");
            }
        } else {
            throw new NotFoundHttpException("Invalid or expired token");
        }
    }


    // don'$this->do thos in production please this is for testing only
    public function actionToa()
    {
        $users = County::find()->all();

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
//                return "Activated successfully, Waiting for frontend guys now";
                return $this->redirect('https://2d0e-41-90-101-26.ngrok-free.app');
            } else {
                throw new BadRequestHttpException("Activation Failed");
            }
        } else {
            throw new BadRequestHttpException("Invalid or Expired Token");
        }
    }


    // get the number of registered users in our website

    public function actionUserstotal() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $users = User::find()->all();
        $total = [];
        if ($users) {
            foreach ($users as $user) {
                $reviewData = [
                    'host_id' => $user->id,
                    'user_name' => $user->first_name . " " . $user->second_name,
                    "email" => $user->email,
                    'roles' => $this->helper($user->id)
                ];

                $total[] = $reviewData;
            }
        } else {
            throw new NotFoundHttpException("No Users  Found, Try again later");
        }

        return ["status" => 200, "message" => "Users retrived succcesifully", "totalUsers" => count($total), "users" => $total];
    }

    // want to update the profile picture here
    public function actionUpdateprofilepic() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $userId = Yii::$app->user->id;

        $user = new User();

        if (UploadedFile::getInstanceByName('imageFile')) {
            $user->imageFile = UploadedFile::getInstanceByName('imageFile');
            if (1) {

                $uploadsDir = Yii::getAlias('@app/uploads/users');
                if (!is_dir($uploadsDir)) {
                    if (!mkdir($uploadsDir, 0755, true)) {
                        Yii::error("Failed to create directory: " . $uploadsDir);
                        throw new \yii\web\ServerErrorHttpException("Failed to create directory: " . $uploadsDir);
                    }
                }
                $uniqueFileName = uniqid() . '.' . $user->imageFile->extension;
                $relativePath = 'uploads/users/' . $uniqueFileName;
                $absolutePath = Yii::getAlias('@app/') . $relativePath;

                if ($user->imageFile->saveAs($absolutePath)) {
                    $updateCommand = Yii::$app->db->createCommand()
                        ->update('user', ['profilePic' => $relativePath], ['id' => $userId])
                        ->execute();

                    if ($updateCommand) {
                        return ['status' => 200, 'message' => 'Updated profile picture successful'];
                    } else {
                        throw new BadRequestHttpException("Failed to update profile pic,,please try again");
                    }
                } else {
                    Yii::error("Failed to save the file to: " . $absolutePath);
                    throw new \yii\web\ServerErrorHttpException("Failed to save the file to: " . $absolutePath);
                }
            } else {
                // Output validation errors
                Yii::error("Validation of Profile Picture failed: " . json_encode($user->getErrors()));
                return "Validation of Profile Picture Failed failed: " . json_encode($user->getErrors());
            }
        }

    }

    public function helper ($id) {
        // Get the authManager component
        $roles = Yii::$app->authManager->getRolesByUser($id);
        return array_keys($roles);
    }

}
?>
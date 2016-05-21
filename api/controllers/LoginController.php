<?php
/**
 * Created by PhpStorm.
 * Project: VanHackathon May 2016
 * User: joao
 * Email: joao@jjmf.com
 * Date: 20/05/16
 * Time: 22:50
 */

namespace api\controllers;

use yii;
use common\models\User;

/**
 * Class LoginController
 *
 * This controller provides an endpoint to identify the user by a token.
 * The token is returned to the user to confirm that it is valid. Otherwise an error message is returned.
 * The user must send the token in every subsequent request.
 *
 * POST or GET verbs are accepted when a token is sent.
 * Creating a new account requires the use of the POST verb.
 *
 * @package api\controllers
 */
class LoginController extends MastermindController
{
    public function actionIndex()
    {
        # Token can be received by GET or POST.
        $token = Yii::$app->request->post('token', Yii::$app->request->get('token'));

        # Name and Email must be sent in a POST request.
        $name = Yii::$app->request->post('name');
        $email = Yii::$app->request->post('email');
        
        # If no token specified, understand as a new user registering. Create the new user and return the generated token.
        # If a token is specified, check if the user exists and confirm the login by returning the token.
        if ($token === null) {
            # Check if name and email were sent.
            if ($name === null) {
                return self::returnError('Tell us your name.');
            }
            if ($email === null) {
                return self::returnError('Tell us your e-mail.');
            }
            
            # Validate email
            $validator = new yii\validators\EmailValidator();
            if (!$validator->validate($email)) {
                return self::returnError('Wrong e-mail format.');
            }

            # Create a new user if not already registered
            $user = User::findByEmail($email);
            if ($user === null) {
                /** @var User $user */
                $user = new User();
                $user->username = Yii::$app->request->post('email');
                $user->email = Yii::$app->request->post('email');
                $user->name = Yii::$app->request->post('name');
                $user->access_token = Yii::$app->getSecurity()->generateRandomString();
                $user->save();

                return ['token' => $user->access_token];
            } else {
                return ['token' => $user->access_token];
            }
        } else {
            # Validate the token
            $user = User::findIdentityByAccessToken($token);
            if ($user !== null) {
                return ['token' => $user->access_token];
            } else {
                return $this->returnError('Invalid access token.');
            }
        }
    }
}

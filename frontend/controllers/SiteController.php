<?php
namespace frontend\controllers;

use yii;
use yii\base\InvalidParamException;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\View;

/**
 * Site controller
 * 
 * This is the main controller of the frontend.
 * This is using websockets and in order to make it work you must have:
 * - Nodejs installed with the following dependencies: express, socket.io and redis
 * - Redis installed
 * 
 * The nodejs application is in nodejs/server.js file.
 * There you can configure:
 * - The nodejs server listening port
 * - The redis server listening port (for communication between node and redis)
 * - The redis server host address (for communication between node and redis)
 * 
 * The redis configuration for the webserver to comunicate with it is in frontend/config/main.php file.
 * Look for the  'redis' under the 'components' array.
 * 
 * For the clients (browsers) to communicate with the nodejs server (our websocket)
 * you configure the 'websocketAddress' parameter in frontend/config/params.php file.
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->registerWebsocketAddress();

        $this->view->title = 'Mastermind';

        # Player have a valid token in a cookie?
        // get the cookie collection (yii\web\CookieCollection) from the "request" component
        $cookies = Yii::$app->request->cookies;
        $token = $cookies->getValue('mastermind-token', '');
        Yii::$app->view->registerJs("var token = '" . $token . "';", View::POS_HEAD);

        return $this->render('index', ['token' => $token]);
    }

    /**
     * Displays the chat page.
     * @return mixed
     */
    public function actionChat()
    {
        $this->registerWebsocketAddress();

        # Player have a valid token in a cookie?
        // get the cookie collection (yii\web\CookieCollection) from the "request" component
        $cookies = Yii::$app->request->cookies;
        $token = $cookies->getValue('mastermind-token', '');
        Yii::$app->view->registerJs("var token = '" . $token . "';", View::POS_HEAD);

        if (Yii::$app->request->post()) {
            $name = Yii::$app->request->post('name');
            $message = Yii::$app->request->post('message');

            return Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'notification',
                'message' => Json::encode(['name' => $name, 'message' => $message])
            ]);
        }

        return $this->render('chat');
    }

    /**
     * Logs in a user.
     * For simplicity we send the player name and email to the api to get an access token.
     * Then store the token in a cookie for the next time.
     * @return mixed
     */
    public function actionLogin()
    {
        $data = [
            'name' => Yii::$app->request->post('name'),
            'email' => Yii::$app->request->post('email')
        ];

        $response = $this->requestApi('login', 'POST', $data);
        
        # Also store the token on session
        Yii::$app->session['token'] = $response['token'];

        # Get the cookie collection (yii\web\CookieCollection) from the "response" component
        $cookies = Yii::$app->response->cookies;

        # Add a new cookie to the response to be sent
        $cookies->add(new \yii\web\Cookie([
            'name' => 'mastermind-token',
            'value' => $response['token'],
            'expire' => strtotime('now + 30 days')
        ]));

        Yii::$app->response->format = 'json';

        return $response;
    }


    ### AJAX ###

    /**
     * The list of open games
     * @return string
     */
    public function actionAjaxGamesList()
    {
        $response = $this->requestApi('v1/game');

        return $this->renderPartial('_gamesList', ['response' => $response]);
    }

    /** Host new game */
    public function actionAjaxHostGame()
    {
        $response = $this->requestApi('v1/game/new', 'POST',  ['secret_size' => Yii::$app->request->post('secret_size')]);

        Yii::$app->response->format = 'json';

        return $response;
    }

    /**
     * Join a games
     * @return string
     */
    public function actionAjaxJoinGame()
    {
        $response = $this->requestApi('v1/game/join', 'POST', ['id' => Yii::$app->request->post('id')]);
        if (isset($response['message'])) {
            # The player is already in this match.
            $response = $this->requestApi('v1/game', 'GET', ['id' => Yii::$app->request->post('id')]);
            //\yii\helpers\VarDumper::dump($response,10,true); die;
        }

        return $this->renderPartial('_gamesRoom', ['response' => $response]);
    }

    /**
     * Sets a player status.
     * If all players are with status = 'ready' emit a startgame message.
     * @return mixed
     */
    public function actionAjaxSetPlayerStatus()
    {
        $response = $this->requestApi('v1/game/player-status', 'POST',  [
            'idGame' => Yii::$app->request->post('idGame'),
            'idPlayer' => Yii::$app->request->post('idPlayer'),
            'status' => Yii::$app->request->post('status')
        ]);

        # Check if all Players are ready
        $allPlayersReady = true;
        foreach ($response as $player) {
            if ($player['player_status'] != 'ready') {
                $allPlayersReady = false;
                break;
            }
        }

        if ($allPlayersReady) {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'notification',
                'message' => Json::encode(['task' => 'startgame', 'idGame' => Yii::$app->request->post('idGame')])
            ]);
        }

        Yii::$app->response->format = 'json';

        return $response;
    }

    
    public function actionAjaxGetGameBoard()
    {
        $idGame = Yii::$app->request->post('idGame');
        $response = $this->requestApi('v1/match/start', 'GET', ['id' => $idGame]);
        if (isset($response['message'])) {
            $response = $this->requestApi('v1/game', 'GET', ['id' => $idGame]);
        }

        return $this->renderPartial('_gameBoard', ['game' => $response]);
    }





    /**
     * Register a global javascript variable with the address of our websocket server.
     * This will be used by frontend/web/js/notification.js
     */
    private function registerWebsocketAddress()
    {
        Yii::$app->view->registerJs("var websocketAddress = '" . Yii::$app->params['websocketAddress'] . "';", View::POS_HEAD);
    }

    /**
     * Make a request to the Mastermind API.
     * @param string $action
     * @param array $params
     * @param string $method
     * @return mixed
     */
    public function requestApi($action, $method = 'GET', $params = [])
    {
        $apiUrl = Yii::$app->params['apiAddress'] . "/{$action}?";

        if (isset($_SESSION['token'])) {
            $apiUrl .= "token=" . $_SESSION['token'];
        }

        //\yii\helpers\VarDumper::dump(http_build_query($params),10,true); die;

        if (strtolower($method) == 'get') {
            $apiUrl .= '&' . http_build_query($params);
        } else {
            $params = http_build_query($params);
        }
        //\yii\helpers\VarDumper::dump($apiUrl,10); die;
        $result = $this->executeCurl($apiUrl, $method, $params);

        return $result;
    }

    /**
     * Executes CURL
     * @param string $url
     * @param bool $params
     * @param string $method
     * @return mixed
     */
    public function executeCurl($url, $method, $params = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
        if ($params !== false && $method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1); // TRUE to do a regular HTTP POST.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params); // The full data to post in a HTTP "POST" operation.

            # SSL parameters
            if (substr($url, 0, 5) === 'https') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                //curl_setopt($ch, CURLOPT_CAINFO, Yii::$app->params['CA.certificatePath']);
            }
        }

        $response = json_decode(curl_exec($ch), true);

        curl_close($ch);

        return $response;
    }



















    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}

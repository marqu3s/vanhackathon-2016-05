<?php
/**
 * Created by PhpStorm.
 * Project: VanHackathon May 2016
 * User: joao
 * Email: joao@jjmf.com
 * Date: 20/05/16
 * Time: 22:50
 */

namespace api\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use api\controllers\MastermindController;
use api\modules\v1\models\Player;

class PlayerController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Player';

    /**
     * Get a Player by its token sent via GET or POST
     * @return Player
     */
    public static function getPlayer()
    {
        $token = Yii::$app->request->post('token', Yii::$app->request->get('token'));
        $player = Player::findIdentityByAccessToken($token);
        if ($player === null) {
            return MastermindController::returnError('Invalid access token.');
        } else {
            return $player;
        }
    }
}

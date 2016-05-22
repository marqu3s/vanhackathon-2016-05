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

use api\modules\v1\models\Match;
use yii;
use yii\rest\ActiveController;
use api\controllers\MastermindController;
use api\modules\v1\models\Player;

/**
 * Class PlayerController
 * 
 * Endpoits for managing players.
 *
 * @package api\modules\v1\controllers
 */
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

    /**
     * Return a player data by its token
     * @return \api\modules\v1\models\Player
     */
    public function actionGetPlayer()
    {
        return self::getPlayer();
    }

    /**
     * Returns all the matches the player had joined and that are active.
     * @return Match[]
     */
    public function actionActiveMatches()
    {
        $player = self::getPlayer();

        return $player->getMatches()->active()->all();
    }

    /**
     * Returns all the matches the player had joined and that are inactive (finished).
     * @return Match[]
     */
    public function actionInactiveMatches()
    {
        $player = self::getPlayer();

        return $player->getMatches()->inactive()->all();
    }

    /**
     * Returns all the matches the player had joined.
     * @return Match[]
     */
    public function actionMatches()
    {
        $player = self::getPlayer();

        return $player->getMatches()->all();
    }
}

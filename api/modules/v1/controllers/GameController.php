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
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use api\controllers\MastermindController;
use api\modules\v1\models\Game;
use api\modules\v1\models\Match;

/**
 * Class GameController
 *
 * Endpoint for managing games.
 *
 * @package api\modules\v1\controllers
 */
class GameController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Game';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'tokenParam' => 'token',
        ];

        return $behaviors;
    }

    /**
     * A player creates a new game.
     * @return Game
     */
    public function actionNew()
    {
        $player = PlayerController::getPlayer();
        $game = new Game();
        $game->id_player_owner = $player->id;
        $game->save();

        # Remove the generated code before sending a response.
        # This way the play will not know the secret code by inspecting network traffic.
        //$game->code = "Shhh! It's a secret!";

        # Automaticaly create a match and add the player
        $match = new Match();
        $match->id_game = $game->id;
        $match->id_player = $player->id;
        $match->save();

        return $match;
    }

    /**
     * A player joins a game.
     * @return Match
     */
    public function actionJoin()
    {
        $player = PlayerController::getPlayer();

        $idGame = (int) Yii::$app->request->post('id');

        $match = new Match();
        $match->id_game = $idGame;
        $match->id_player = $player->id;
        if (!$match->validate()) {
            $errors = $match->getErrors();

            return MastermindController::returnError(reset($errors));
        } else {
            $match->save();

            return $match;
        }
    }

    /**
     * A player leaves a game.
     * @return string|bool true on success
     */
    public function actionLeave()
    {
        $player = PlayerController::getPlayer();

        $idGame = (int) Yii::$app->request->post('id');

        $match = Match::find()->where(['id_game' => $idGame, 'id_player' => $player->id])->one();
        if ($match !== null) $match->delete();

        return true;
    }
    
}

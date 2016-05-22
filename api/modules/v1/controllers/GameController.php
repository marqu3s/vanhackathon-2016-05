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
    
    public function actions()
    {
        return [];
    }

    /**
     * Return a list of the open games
     */
    public function actionIndex($id = null)
    {
        $where = 'game.started_at IS NULL';
        if ($id !== null) $where .= ' AND game.id = ' . $id;

        $query = Game::find()
            ->with(['players', 'matches'])
            ->where($where)
            ->orderBy(['game.id' => SORT_ASC])
            ->asArray();

        if ($id !== null) {
            $games = $query->one();
        } else {
            $games = $query->all();
        }

        return $games;
    }

    /**
     * A player creates a new game.
     * @return Game
     */
    public function actionNew()
    {
        $secretSize = (int) Yii::$app->request->post('secret_size');
        if ($secretSize < Game::MIN_SECRET_SIZE OR $secretSize > Game::MAX_SECRET_SIZE) {
            return MastermindController::returnError('The secret code size must be between ' . Game::MIN_SECRET_SIZE . ' and ' . Game::MAX_SECRET_SIZE . '.');
        }

        $player = PlayerController::getPlayer();

        # Create a new game and set the player as the owner.
        $game = new Game();
        $game->secretSize = $secretSize;
        $game->id_player_owner = $player->id;
        $game->save();

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
            $game = Game::find()->with(['players', 'matches'])->where("id = $idGame")->asArray()->one();

            return $game;
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

    /**
     * Set (POST verb) or get (GET verb) a player status in a match.
     * @return \api\modules\v1\models\Match[]
     * @throws \Exception
     */
    public function actionPlayerStatus()
    {
        $idGame = (int) Yii::$app->request->post('idGame', Yii::$app->request->get('idGame'));
        $idPlayer = (int) Yii::$app->request->post('idPlayer', Yii::$app->request->get('idPlayer'));
        $status = Yii::$app->request->post('status');

        $result = [];
        if (Yii::$app->request->method == 'POST') {
            # Set player status
            $match = Match::find()->where("id_game = $idGame AND id_player = $idPlayer")->one();
            $match->player_status = $status;
            $match->update();

            # Get all player status
            $result = Match::find()->where("id_game = $idGame")->all();
        } elseif (Yii::$app->request->method == 'GET') {
            if ($idPlayer === 0) {
                $result = Match::find()->where("id_game = $idGame")->all();
            } else {
                $result = Match::find()->where("id_game = $idGame AND id_player = $idPlayer")->one();
            }
        }

        return $result;
    }
    
}

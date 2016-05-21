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
     * Creates a new game.
     * @return \common\models\Game
     */
    public function actionNew()
    {
        $player = PlayerController::getPlayer();
        $game = new Game();
        $game->id_player_owner = $player->id;
        $game->save();

        # Remove the generated code before sending a response.
        # This way the play will not know the secret code by inspecting network traffic.
        $game->code = "Shhh! It's a secret!";

        # Automaticaly join the player in the new game
        $match = new Match();
        $match->id_game = $game->id;
        $match->id_player = $player->id;
        $match->save();

        return $game;
    }

    /**
     * A player joins a match.
     * @return string|bool true on success
     */
    public function actionJoin()
    {
        $player = PlayerController::getPlayer();

        $match = new Match();
        $match->id_game = (int) Yii::$app->request->post('id');
        $match->id_player = $player->id;
        if (!$match->validate()) {
            $errors = $match->getErrors();

            return MastermindController::returnError(reset($errors));
        } else {
            $match->save();

            return true;
        }
    }

    /**
     * A player leaves a match.
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
     * A player begins a game.
     * @return bool
     */
    public function actionStart()
    {
        $player = PlayerController::getPlayer();
        $idGame = (int) Yii::$app->request->post('id');
        $game = Game::findOne($idGame);
        $game->started_at = time();
        $game->save();

        return true;
    }

    /**
     * A player nade a guess.
     * @return array
     */
    public function actionGuess()
    {
        $guess = Yii::$app->request->post('guess');
        $guess = explode(',', $guess);
        $idGame = (int) Yii::$app->request->post('id');
        $player = PlayerController::getPlayer();
        $game = Game::findOne($idGame);
        $code = explode(',', $game->code);
        $match = Match::find()->where(['id_game' => $idGame, 'id_player' => $player->id])->one();
        $match->num_guesses++;
        $match->save();

        # Solved the code?
        if ($guess === $code) {
            $game->id_player_winner = $player->id;
            $game->ended_at = time();
            $game->save();

            $game->code = "Shhh! It's a secret!";
            return [
                'game' => $game,
                'message' => 'You won! Congratulations!!!',
                'exact' => count($game->code),
                'near' => 0,
                'solved' => true
            ];
        }

        # TODO: How many exact and near?

        return [
            'game' => $game,
            'message' => 'Not this time...',
            'exact' => 0,
            'near' => 0,
            'solved' => false
        ];

    }
}

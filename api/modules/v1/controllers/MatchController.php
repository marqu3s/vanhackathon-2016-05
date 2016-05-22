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
 * Class MatchController
 *
 * Endpoint for managing player-game relations (matches).
 *
 * @package api\modules\v1\controllers
 */
class MatchController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Match';
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
     * A player starts a match.
     * @return Game
     */
    public function actionStart()
    {
        $idGame = (int) Yii::$app->request->post('id');

        $game = Game::findOne($idGame);
        if ($game->started_at > 0) {
            return MastermindController::returnError('This game has already started.');
        } else {
            $game->started_at = time();
            $game->save();

            return Game::find()->with(['players', 'matches'])->where(['id' => $idGame])->asArray()->one();
        }
    }

    /**
     * A player makes a guess.
     * @return array
     */
    public function actionGuess()
    {
        $player = PlayerController::getPlayer();

        $guess = Yii::$app->request->post('guess');

        $idGame = (int) Yii::$app->request->post('id');

        /** @var Match $match */
        $match = Match::find()->where(['id_game' => $idGame, 'id_player' => $player->id])->one();
        $result = $match->evaluateGuess($guess);

        return $result;
    }
}

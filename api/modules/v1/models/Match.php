<?php

namespace api\modules\v1\models;

use api\modules\v1\controllers\PlayerController;
use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "match".
 *
 * @property integer $id
 * @property integer $id_game
 * @property integer $id_player
 * @property integer $joined_at
 * @property integer $num_guesses
 * @property string $player_status // idle, waitting-others, playing
 *
 * @property Game $game
 * @property Player $player
 */
class Match extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['joined_at'],
                    //ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'match';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_game', 'id_player'], 'required'],
            [['id_game', 'id_player', 'joined_at', 'num_guesses'], 'integer'],
            [['id_game', 'id_player'], 'unique', 'targetAttribute' => ['id_game', 'id_player'], 'message' => 'You are already in this match.'],
            [['player_status'], 'string', 'max' => 20],
            [['id_game'], 'exist', 'skipOnError' => true, 'targetClass' => Game::className(), 'targetAttribute' => ['id_game' => 'id']],
            [['id_player'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['id_player' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_game' => 'Id Game',
            'id_player' => 'Id Player',
            'joined_at' => 'Joined At',
            'num_guesses' => 'Num Guesses',
            'player_status' => 'Player Status',
        ];
    }
    /**
     * @inheritdoc
     * @return MatchQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MatchQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(Game::className(), ['id' => 'id_game']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::className(), ['id' => 'id_player']);
    }

    /**
     * Evaluates a player guess.
     * @param string $guessCode
     * @return array
     */
    public function evaluateGuess($guessCode)
    {
        $exactMatches = 0;
        $nearMatches = 0;

        $this->num_guesses++;
        $this->save();

        $player = PlayerController::getPlayer();

        # Guess code in array format
        $arrGuessCode = explode(',', $guessCode);
        
        # Secret code in array format
        $arrSecretCode = explode(',', $this->game->code);

        # Solved the code?
        if ($arrGuessCode === $arrSecretCode) {
            $solved = true;
            $message = 'You won! Congratulations!!!';
            $exactMatches = count($this->game->code);

            $this->game->id_player_winner = $player->id;
            $this->game->ended_at = time();
            $this->game->save();
        } else {
            $solved = false;
            $message = 'Not this time...';

            # How many exact and near matches?
            foreach ($arrGuessCode as $i => $colorCode) {
                if ($arrSecretCode[$i] == $colorCode) {
                    $exactMatches++;
                } else {
                    $nearMatches++;
                }
            }

            # Remove the secret code before sending a response.
            # This way the play will not know the secret code by inspecting network traffic.
            $this->game->code = "Shhh! It's a secret!";
        }

        # Save player guess, creating a history of guesses.
        $this->savePlayerGuess($player->id, $guessCode, $exactMatches, $nearMatches);

        # Player guess history
        $guessHistory = $player->getPlayerGuessHistories($this->game->id)->all();

        return [
            'solved' => $solved,
            'message' => $message,
            'exact_matches' => $exactMatches,
            'near_matches' => $nearMatches,
            'game' => $this->game,
            'match' => $this,
            'guess_history' => $guessHistory,
        ];
    }

    /**
     * Saves a player guess.
     * @param integer $idPlayer The ID of a player.
     * @param string $guessCode The guessed code.
     * @param integer $exactMatches The number of exact matches/colors for this guess.
     * @param integer $nearMatches The number of near matches/colors for this guess.
     * @return bool
     */
    private function savePlayerGuess($idPlayer, $guessCode, $exactMatches, $nearMatches)
    {
        $model = new PlayerGuessHistory();
        $model->id_game = $this->game->id;
        $model->id_player = $idPlayer;
        $model->guess = $guessCode;
        $model->exact_matches = $exactMatches;
        $model->near_matches = $nearMatches;

        return $model->save();
    }
}

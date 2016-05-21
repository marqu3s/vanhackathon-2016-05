<?php

namespace api\modules\v1\models;

use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "player_guess_history".
 *
 * @property integer $id
 * @property integer $id_game
 * @property integer $id_player
 * @property string $guess
 * @property integer $exact_matches
 * @property integer $near_matches
 * @property integer $guessed_at
 *
 * @property Game $idGame
 * @property Player $idPlayer
 */
class PlayerGuessHistory extends ActiveRecord
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['guessed_at'],
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
        return 'player_guess_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_game', 'id_player', 'guess'], 'required'],
            [['id_game', 'id_player', 'exact_matches', 'near_matches', 'guessed_at'], 'integer'],
            [['guess'], 'string', 'max' => 15],
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
            'guess' => 'Guess',
            'exact_matches' => 'Exact Colors',
            'near_matches' => 'Near Colors',
            'guessed_at' => 'Guessed At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdGame()
    {
        return $this->hasOne(Game::className(), ['id' => 'id_game']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdPlayer()
    {
        return $this->hasOne(Player::className(), ['id' => 'id_player']);
    }
}

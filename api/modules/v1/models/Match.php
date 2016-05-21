<?php

namespace api\modules\v1\models;

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
 *
 * @property Game $idGame
 * @property Player $idPlayer
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

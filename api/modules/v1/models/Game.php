<?php

namespace api\modules\v1\models;

use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "game".
 *
 * @property integer $id
 * @property string $available_colors
 * @property string $code
 * @property integer $id_player_owner
 * @property integer $id_player_winner
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $started_at
 * @property integer $ended_at
 *
 * @property Player $owner
 * @property Player $winner
 * @property Match[] $matches
 * @property Player[] $players
 */
class Game extends \yii\db\ActiveRecord
{
    CONST MIN_SECRET_SIZE = 2;
    CONST MAX_SECRET_SIZE = 8;

    public $secretSize = 4;

    private $_colors = [
        'R' => 'Red',
        'B' => 'Blue',
        'G' => 'Green',
        'Y' => 'Yellow',
        'O' => 'Orange',
        'P' => 'Purple',
        'C' => 'Cyan',
        'M' => 'Magenta'
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'game';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_player_owner', 'id_player_winner', 'created_at', 'updated_at', 'started_at', 'ended_at'], 'integer'],
            [['available_colors', 'code'], 'string', 'max' => 15],
            [['id_player_owner'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['id_player_owner' => 'id']],
            [['id_player_winner'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['id_player_winner' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'available_colors' => 'Available Colors',
            'code' => 'Code',
            'id_player_owner' => 'Id Player Owner',
            'id_player_winner' => 'Id Player Winner',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'started_at' => 'Started At',
            'ended_at' => 'Ended At',
        ];
    }

    /**
     * @param bool $insert true if inserting a record in the database.
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) { // only to this when inserting a new game into the database
                $this->available_colors = implode(',', array_keys($this->_colors));

                # Randomize the order of the colors and set the code for this game.
                $colors = $this->_colors;
                $this->shuffle_assoc($colors);


                # Generate a secret code that have the size of $thi->secretSize
                $this->code = array_slice($colors, 0, $this->secretSize);
                $this->code = implode(',', array_keys($this->code));
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Player::className(), ['id' => 'id_player_owner']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWinner()
    {
        return $this->hasOne(Player::className(), ['id' => 'id_player_winner']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::className(), ['id' => 'id_player'])->viaTable('match', ['id_game' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatches()
    {
        return $this->hasMany(Match::className(), ['id_game' => 'id']);
    }

    /**
     * Check if a game has started.
     * @return bool true if the game has started.
     */
    public function isStarted()
    {
        return $this->started_at > 0;
    }

    /**
     * Check if a game has ended.
     * @return bool true if the game has ended.
     */
    public function isEnded()
    {
        return $this->ended_at > 0;
    }



    ### AUXILIARY FUNCTIONS ###

    /**
     * @see http://php.net/manual/pt_BR/function.shuffle.php
     */
    function shuffle_assoc(&$array)
    {
        $keys = array_keys($array);

        shuffle($keys);

        $new = [];
        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }

        $array = $new;

        return true;
    }
   
}

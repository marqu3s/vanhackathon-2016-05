<?php

namespace api\modules\v1\models;

/**
 * This is the ActiveQuery class for [[Match]].
 *
 * This class allows us to define functions that act as a query modifier for querying models.
 *
 * @see Match
 */
class MatchQuery extends \yii\db\ActiveQuery
{
    /**
     * Filters matches by the active status.
     * A Match is considered active if the field started_at is greater than 0.
     * @return $this
     */
    public function active()
    {
        return $this->joinWith('game')
            ->andWhere('game.started_at > 0 AND (game.ended_at = 0 OR game.ended_at IS NULL)');
    }

    /**
     * Filters matches by the active status.
     * A Match is considered inactive if the field ended_at is greater than 0.
     * @return $this
     */
    public function inactive()
    {
        return $this->joinWith('game')
            ->andWhere('game.ended_at > 0');
    }

    /**
     * @inheritdoc
     * @return Match[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Match|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

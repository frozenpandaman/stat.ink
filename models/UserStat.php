<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_stat".
 *
 * @property integer $user_id
 * @property integer $battle_count
 * @property string $wp
 * @property string $wp_short
 * @property integer $total_kill
 * @property integer $total_death
 * @property integer $nawabari_count
 * @property string $nawabari_wp
 * @property integer $nawabari_kill
 * @property integer $nawabari_death
 * @property integer $gachi_count
 * @property string $gachi_wp
 * @property integer $gachi_kill
 * @property integer $gachi_death
 * @property integer $total_kd_battle_count
 *
 * @property User $user
 */
class UserStat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_stat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'battle_count', 'total_kill', 'total_death', 'total_kd_battle_count'], 'integer'],
            [['nawabari_count', 'nawabari_kill', 'nawabari_death'], 'integer'],
            [['gachi_count', 'gachi_kill', 'gachi_death'], 'integer'],
            [['wp', 'wp_short', 'nawabari_wp', 'gachi_wp'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'battle_count' => 'Battle Count',
            'wp' => 'Wp',
            'wp_short' => 'Wp Short',
            'total_kill' => 'Total Kill',
            'total_death' => 'Total Death',
            'nawabari_count' => 'Nawabari Count',
            'nawabari_wp' => 'Nawabari Wp',
            'nawabari_kill' => 'Nawabari Kill',
            'nawabari_death' => 'Nawabari Death',
            'gachi_count' => 'Gachi Count',
            'gachi_wp' => 'Gachi Wp',
            'gachi_kill' => 'Gachi Kill',
            'gachi_death' => 'Gachi Death',
            'total_kd_battle_count' => 'Total KD Battle Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function createCurrentData()
    {
        $db = Yii::$app->db;
        static $nawabari = null;
        if ($nawabari === null) {
            $nawabari = Rule::findOne(['key' => 'nawabari'])->id;
        }
        $condIsNawabari = sprintf('({{battle}}.[[rule_id]] = %s)', $db->quoteValue($nawabari));
        $condIsGachi = sprintf('({{battle}}.[[rule_id]] <> %s)', $db->quoteValue($nawabari));

        static $private = null;
        if ($private === null) {
            $private = Lobby::findOne(['key' => 'private'])->id;
        }
        $condIsNotPrivate = sprintf(
            '({{battle}}.[[lobby_id]] IS NULL OR {{battle}}.[[lobby_id]] <> %s)',
            $db->quoteValue($private)
        );

        $now = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
        $cond24Hours = sprintf(
            '(({{battle}}.[[end_at]] IS NOT NULL) AND ({{battle}}.[[end_at]] BETWEEN %s AND %s))',
            $db->quoteValue(gmdate('Y-m-d H:i:sO', $now - 86400 + 1)),
            $db->quoteValue(gmdate('Y-m-d H:i:sO', $now))
        );

        $condKDPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle}}.[[kill]] IS NOT NULL',
            '{{battle}}.[[death]] IS NOT NULL',
        ]));

        $column_battle_count = "COUNT(*)";
        $column_wp = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    '{{battle}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    '{{battle}}.[[is_win]] IS NOT NULL',
                ])
            )
        );
        $column_wp_short = sprintf(
            "(%s * 100.0 / NULLIF(%s, 0))",
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $cond24Hours,
                    '{{battle}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $cond24Hours,
                    '{{battle}}.[[is_win]] IS NOT NULL',
                ])
            )
        );
        $column_total_kd_battle_count = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condKDPresent,
            ])
        );
        $column_total_kill = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condKDPresent,
            ])
        );
        $column_total_death = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[death]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condKDPresent,
            ])
        );
        $column_nawabari_count = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsNawabari,
            ])
        );
        $column_nawabari_wp = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $condIsNawabari,
                    '{{battle}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $condIsNawabari,
                    '{{battle}}.[[is_win]] IS NOT NULL',
                ])
            )
        );
        $column_nawabari_kill = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsNawabari,
                $condKDPresent,
            ])
        );
        $column_nawabari_death = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[death]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsNawabari,
                $condKDPresent,
            ])
        );

        $column_gachi_count = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsGachi,
            ])
        );
        $column_gachi_wp = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $condIsGachi,
                    '{{battle}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $condIsGachi,
                    '{{battle}}.[[is_win]] IS NOT NULL',
                ])
            )
        );
        $column_gachi_kill = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsGachi,
                $condKDPresent,
            ])
        );
        $column_gachi_death = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[death]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsGachi,
                $condKDPresent,
            ])
        );

        $query = (new \yii\db\Query())
            ->select([
                'battle_count'      => $column_battle_count,
                'wp'                => $column_wp,
                'wp_short'          => $column_wp_short,
                'total_kill'        => $column_total_kill,
                'total_death'       => $column_total_death,
                'total_kd_battle_count' => $column_total_kd_battle_count,
                'nawabari_count'    => $column_nawabari_count,
                'nawabari_wp'       => $column_nawabari_wp,
                'nawabari_kill'     => $column_nawabari_kill,
                'nawabari_death'    => $column_nawabari_death,
                'gachi_count'       => $column_gachi_count,
                'gachi_wp'          => $column_gachi_wp,
                'gachi_kill'        => $column_gachi_kill,
                'gachi_death'       => $column_gachi_death,
            ])
            ->from('battle')
            ->andWhere(['{{battle}}.[[user_id]]' => $this->user_id]);

        $this->attributes =  $query->createCommand()->queryOne();
        
        $keys = [
            'battle_count', 'total_kill', 'total_death', 'total_kd_battle_count',
            'nawabari_count', 'nawabari_kill', 'nawabari_death',
            'gachi_count', 'gachi_kill', 'gachi_death',
        ];
        foreach ($keys as $key) {
            $this->$key = (int)$this->$key;
        }

        return $this;
    }

    public function toJsonArray()
    {
        return [
            'entire' => [
                'battle_count'  => $this->battle_count,
                'wp'            => $this->wp === null ? null : (float)$this->wp,
                'wp_24h'        => $this->wp_short === null ? null : (float)$this->wp_short,
                'kill'          => $this->total_kill,
                'death'         => $this->total_death,
                'kd_available_battle' => $this->total_kd_battle_count,
            ],
            'nawabari' => [
                'battle_count'  => $this->nawabari_count,
                'wp'            => $this->nawabari_wp === null ? null : (float)$this->nawabari_wp,
                'kill'          => $this->nawabari_kill,
                'death'         => $this->nawabari_death,
            ],
            'gachi' => [
                'battle_count'  => $this->gachi_count,
                'wp'            => $this->gachi_wp === null ? null : (float)$this->gachi_wp,
                'kill'          => $this->gachi_kill,
                'death'         => $this->gachi_death,
            ],
        ];
    }
}

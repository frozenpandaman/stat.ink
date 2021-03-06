<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\Model;

class BattleForm extends Model
{
    public $lobby_id;
    public $rule_id;
    public $map_id;
    public $weapon_id;

    public function rules()
    {
        return [
            [['lobby_id'], 'exist',
                'targetClass' => Lobby::className(),
                'targetAttribute' => 'id'],
            [['rule_id'], 'exist',
                'targetClass' => Rule::className(),
                'targetAttribute' => 'id'],
            [['map_id'], 'exist',
                'targetClass' => Map::className(),
                'targetAttribute' => 'id'],
            [['weapon_id'], 'exist',
                'targetClass' => Weapon::className(),
                'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lobby_id' => Yii::t('app', 'Lobby'),
            'rule_id' => Yii::t('app', 'Mode'),
            'map_id' => Yii::t('app', 'Map'),
            'weapon_id' => Yii::t('app', 'Weapon'),
        ];
    }
}

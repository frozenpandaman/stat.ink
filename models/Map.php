<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use DateTimeZone;
use Yii;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\Translator;

/**
 * This is the model class for table "map".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $area
 * @property string $release_at
 *
 * @property Battle[] $battles
 * @property PeriodMap[] $periodMaps
 * @property SplapiMap[] $splapiMaps
 */
class Map extends \yii\db\ActiveRecord
{
    use SafeFindOneTrait;

    public static function findAvailable()
    {
        $now = (int)(@$_SERVER['REQUEST_TIME'] ?: time());
        return static::find()
            ->andWhere('{{map}}.[[release_at]] IS NOT NULL')
            ->andWhere(['<=', '{{map}}.[[release_at]]', gmdate('Y-m-d\TH:i:sP', $now)]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['area'], 'integer'],
            [['release_at'], 'safe'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'area' => 'Area',
            'release_at' => 'Release At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::className(), ['map_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriodMaps()
    {
        return $this->hasMany(PeriodMap::className(), ['map_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSplapiMaps()
    {
        return $this->hasMany(SplapiMap::className(), ['map_id' => 'id']);
    }

    public function toJsonArray()
    {
        $t = $this->release_at ? strtotime($this->release_at) : null;
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-map', $this->name),
            'area' => $this->area,
            'release_at' => $t
                ? DateTimeFormatter::unixTimeToJsonArray(
                    $t,
                    new DateTimeZone('Etc/UTC')
                )
                : null,
        ];
    }
}

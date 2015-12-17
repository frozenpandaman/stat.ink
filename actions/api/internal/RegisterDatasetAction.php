<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\internal;

use Yii;
use yii\web\ViewAction;
use app\models\Map;
use app\models\PeriodMap;
use app\models\User;
use app\models\Weapon;
use app\models\WeaponType;
use app\models\Battle;

class RegisterDatasetAction extends ViewAction
{
    private $user;
    private $now;

    public function init()
    {
        $this->user = Yii::$app->user->identity;
        $this->now = (int)(@$_SERVER['REQUEST_TIME'] ?: time());
        return parent::init();
    }

    public function run()
    {
        $resp = Yii::$app->response;
        $resp->format = 'compact-json';
        $resp->statusCode = 200;
        
        if (!$this->user) {
            return [
                'error' => 'not authorized',
            ];
        }

        try {
            $ret = [
                'error' => null,
                'user' => [
                    'id' => $this->user->id,
                    'screen_name' => $this->user->screen_name,
                    'name' => $this->user->name,
                ],
                'rules' => [
                    'regular' => $this->getRuleMap('regular'),
                    'gachi' => $this->getRuleMap('gachi'),
                ],
                'maps' => $this->getMaps(),
                'weapons' => $this->getWeapons(),
                'lastWeapon' => $this->getLastWeapon(),
                'favWeapons' => $this->getFavWeapons(),
            ];
            return $ret;
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getRuleMap($modeKey)
    {
        $period = \app\components\helpers\Battle::calcPeriod($this->now);
        if (!$models = PeriodMap::findByModeAndPeriod($modeKey, $period)->all()) {
            throw new \Exception('rule/map not ready');
        }
        $rule = null;
        $maps = [];
        foreach ($models as $model) {
            if ($rule === null) {
                $rule = [
                    'key' => $model->rule->key,
                    'name' => Yii::t('app-rule', $model->rule->name),
                ];
            }
            $maps[] = $model->map->key;
        }
        return [
            'rule' => $rule,
            'maps' => $maps,
        ];
    }

    public function getMaps()
    {
        $ret = [];
        foreach (Map::findAvailable()->all() as $map) {
            $ret[$map->key] = Yii::t('app-map', $map->name);
        }
        uasort($ret, 'strnatcasecmp');
        return $ret;
    }

    public function getWeapons()
    {
        $ret = [];
        foreach (WeaponType::find()->orderBy('id ASC')->all() as $type) {
            $group = [];
            $weapons = Weapon::findAvailable()
                ->andWhere(['{{weapon}}.[[type_id]]' => $type->id])
                ->all();
            foreach ($weapons as $weapon) {
                $group[$weapon->key] = Yii::t('app-weapon', $weapon->name);
            }
            uasort($group, 'strnatcasecmp');
            $ret = array_merge($ret, $group);
        }
        return $ret;
    }

    public function getLastWeapon()
    {
        $battle = Battle::find()
            ->with('weapon')
            ->innerJoinWith('weapon')
            ->andWhere(['{{battle}}.[[user_id]]' => $this->user->id])
            ->orderBy('{{battle}}.[[id]] DESC')
            ->limit(1)
            ->one();
        return $battle ? $battle->weapon->key : null;
    }

    public function getFavWeapons()
    {
        $ret = [];
        $query = $this->user->getUserWeapons()
            ->orderBy('[[count]] DESC')
            ->limit(10);
        foreach ($query->all() as $model) {
            $ret[] = $model->weapon->key;
        }
        return $ret;
    }
}

<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use yii\base\Component;
use yii\helpers\Json;
use yii\web\ResponseFormatterInterface;

class CompactJsonResponseFormatter extends Component implements ResponseFormatterInterface
{
    public function format($response)
    {
        $response->getHeaders()->set('Content-Type', 'application/json; charset=UTF-8');
        if ($response->data !== null) {
            $response->content = Json::encode(
                $response->data,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
        }
    }
}

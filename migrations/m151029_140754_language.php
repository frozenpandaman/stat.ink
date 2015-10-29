<?php
use yii\db\Migration;

class m151029_140754_language extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('language', ['lang', 'name', 'name_en'], [
            [ 'en-GB', 'English(UK)', 'English(UK)' ],
            [ 'fr-FR', 'Français(France)', 'French(France)' ],
            [ 'fr-CA', 'Français(Canada)', 'French(Canada)' ],
        ]);
    }

    public function down()
    {
        $this->delete('language', ['lang' => [
            'en-GB',
            'fr-FR',
            'fr-CA',
        ]]);
    }
}

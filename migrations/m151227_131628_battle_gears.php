<?php
use yii\db\Migration;

class m151227_131628_battle_gears extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'ADD COLUMN [[headgear_id]] BIGINT NULL',
            'ADD COLUMN [[clothing_id]] BIGINT NULL',
            'ADD COLUMN [[shoes_id]] BIGINT NULL',
        ]));
        $this->addForeignKey('fk_battle_13', 'battle', 'headgear_id', 'gear_configuration', 'id');
        $this->addForeignKey('fk_battle_14', 'battle', 'clothing_id', 'gear_configuration', 'id');
        $this->addForeignKey('fk_battle_15', 'battle', 'shoes_id',    'gear_configuration', 'id');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'DROP COLUMN [[headgear_id]]',
            'DROP COLUMN [[clothing_id]]',
            'DROP COLUMN [[shoes_id]]',
        ]));
    }
}

<?php

use yii\db\Migration;

class m170112_005945_data extends Migration
{

    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__.'/data/dump.sql'));
    }

    public function safeDown()
    {
    }
}

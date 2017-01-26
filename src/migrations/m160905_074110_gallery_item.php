<?php

namespace lo\modules\gallery\migrations;

use lo\core\db\Migration;

class m160905_074110_gallery_item extends Migration
{
    public $tableGroup = "gallery";

    const TBL = 'item';

    public function up()
    {
        $this->createTable($this->tn(self::TBL), [
            'id' => $this->primaryKey(),
            'status' => 'tinyint(1) NOT NULL DEFAULT 0',
            'author_id' => $this->integer()->notNull(),
            'updater_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),

            'entity' => $this->string(),
            'owner_id' => $this->string(), // т.к. может быть составной ключ
            'name' => $this->string(),
            'description' => $this->text(),
            'path' => $this->string(),
            'thumb' => $this->string(),
            'image' => $this->string(),
            'pos' => $this->integer()->defaultValue(0),
            'on_main' => 'tinyint(1) NOT NULL DEFAULT 0',
        ]);

        $this->createIndex('idx_gallery_item_status', $this->tn(self::TBL), 'status');
        $this->createIndex('idx_gallery_item_pos', $this->tn(self::TBL), 'pos');
        $this->createIndex('idx_gallery_item_main', $this->tn(self::TBL), 'on_main');
        $this->createIndex('idx_gallery_item_entity', $this->tn(self::TBL), ['entity', 'owner_id']);
    }

    public function down()
    {
        $this->dropTable($this->tn(self::TBL));
    }

}
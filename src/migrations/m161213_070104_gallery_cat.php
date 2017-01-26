<?php
namespace lo\modules\gallery\migrations;

use lo\core\db\Migration;

class m161213_070104_gallery_cat extends Migration
{
    public $tableGroup = "gallery";

    const TBL = 'cat';

    public function up()
    {
        $this->createTable($this->tn(self::TBL), [
            'id' => $this->primaryKey(),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'author_id' => $this->integer()->notNull(),
            'updater_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),

            'name' => $this->string()->notNull(),
            'slug' => $this->string()->unique(),

        ]);

        $this->createIndex('idx_gallery_cat_status', $this->tn(self::TBL), 'status');
    }

    public function down()
    {
        $this->dropTable($this->tn(self::TBL));
    }
}
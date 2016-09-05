<?php

namespace lo\modules\gallery\models;

use lo\core\db\ActiveRecord;

/**
 * This is the model class for table "gallery_item".
 *
 * @property integer $id
 * @property string $name
 * @property string $entity
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class GalleryItem extends ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gallery__item}}';
    }

    /**
     * @inheritdoc
     */
    public function metaClass()
    {
        return GalleryItemMeta::class;
    }

}

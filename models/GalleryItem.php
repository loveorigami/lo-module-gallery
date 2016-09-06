<?php

namespace lo\modules\gallery\models;

use lo\core\db\ActiveRecord;

/**
 * This is the model class for table "gallery_item".
 *
 * @property integer $id
 * @property string $name
 * @property string $entity
 * @property string $image
 * @property string $owner_id
 * @property string $description
 * @property integer $status
 * @property integer $pos
 * @property integer $created_at
 * @property integer $updated_at
 */
class GalleryItem extends ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    const THUMB_TMB = 'tmb';
    const THUMB_BIG = 'big';

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

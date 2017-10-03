<?php

namespace lo\modules\gallery\models;

use lo\core\cache\CacheHelper;
use lo\core\cache\CacheInvalidateBehavior;
use lo\core\db\ActiveRecord;

/**
 * This is the model class for table "gallery__item".
 *
 * @property integer $id
 * @property string $name
 * @property string $entity
 * @property string $image
 * @property string $owner_id
 * @property string $description
 * @property string $path
 * @property string $thumb
 * @property integer $status
 * @property integer $on_main
 * @property integer $pos
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property GalleryCat $cat
 */
class GalleryItem extends ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    const THUMB_TMB = 'tmb';
    const THUMB_BIG = 'big';

    const IMG_KEY = 'imgkey';

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

    /**
     * @return array
     */
    public function behaviors()
    {
        $arr = parent::behaviors();

        $arr["cache"] = [
            'class' => CacheInvalidateBehavior::class,
            'cacheComponent' => CacheHelper::COMMON,
            'tags' => [
                self::IMG_KEY
            ]
        ];
        return $arr;
    }

    /**
     * @return \lo\core\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(GalleryCat::class, ['id' => 'owner_id']);
    }

}

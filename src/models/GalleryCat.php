<?php

namespace lo\modules\gallery\models;

use lo\core\db\ActiveRecord;

/**
 * This is the model class for table "gallery__cat".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property integer $created_at
 * @property integer $updated_at
 */
class GalleryCat extends ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    /**
     * Gallery
     */
    const THUMB_ORI = 'ori'; // original
    const THUMB_BIG = 'big';

    const THUMB_ONE = 'one';
    const THUMB_TMB = 'tmb';



    const GALLERY_ONE = 'gallery';

    public $gal;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gallery__cat}}';
    }

    /**
     * @inheritdoc
     */
    public function metaClass()
    {
        return GalleryCatMeta::class;
    }

}

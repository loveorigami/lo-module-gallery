<?php

namespace lo\modules\gallery\models;

use Yii;


/**
 * This is the model class for table "gallery".
 *
 * @property integer $id
 * @property string $slug
 * @property string $title
 * @property string $body
 * @property string $view
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Gallery extends \lo\core\db\ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gallery}}';
    }

    /**
     * @inheritdoc
     */
    public function metaClass()
    {
        return GalleryMeta::class;
    }

}

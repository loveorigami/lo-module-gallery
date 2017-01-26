<?php
namespace lo\modules\gallery\models;

use Yii;
use lo\core\db\MetaFields;
use lo\core\db\fields;

/**
 * Class GalleryItemMeta
 * @package lo\modules\gallery\models
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class GalleryItemMeta extends MetaFields
{
    /**
     * @return array
     */
    protected function config()
    {
        /** @var GalleryItem $owner */
        $owner = $this->owner;
        return [
            "image" => [
                "definition" => [
                    "class" => fields\ImagePathField::class,
                    "title" => Yii::t('backend', 'Image'),
                    'uploadOptions' => [
                        'path' => '@storagePath'.$owner->path,
                        'url' => '@storageUrl'.$owner->path,
                        'thumbPath' => '@storagePath'.$owner->thumb,
                        'thumbUrl' => '@storageUrl'.$owner->thumb,
                    ]
                ],
                "params" => [$this->owner, "image"]
            ],

            "name" => [
                "definition" => [
                    "class" => fields\TextField::class,
                    "title" => Yii::t('backend', 'Name'),
                    "showInGrid" => true,
                    "showInFilter" => true,
                    "isRequired" => false,
                    "editInGrid" => false,
                ],
                "params" => [$this->owner, "name"]
            ],

            "description" => [
                "definition" => [
                    "class" => fields\TextAreaField::class,
                    "title" => Yii::t('backend', 'Text'),
                    "showInGrid" => false,
                    "isRequired" => false,
                ],
                "params" => [$this->owner, "description"]
            ],

            "owner_id" => [
                "definition" => [
                    "class" => fields\TextField::class,
                    "title" => Yii::t('backend', 'Owner Id'),
                    "showInGrid" => false,
                    "showInFilter" => true,
                    "showInForm" => false,
                ],
                "params" => [$this->owner, "owner_id"]
            ],

            "path" => [
                "definition" => [
                    "class" => fields\TextField::class,
                    "title" => Yii::t('backend', 'Path'),
                    "showInGrid" => false,
                    "showInFilter" => false,
                    "showInForm" => false,
                ],
                "params" => [$this->owner, "path"]
            ],

            "thumb" => [
                "definition" => [
                    "class" => fields\TextField::class,
                    "title" => Yii::t('backend', 'Thumb'),
                    "showInGrid" => false,
                    "showInFilter" => false,
                    "showInForm" => false,
                ],
                "params" => [$this->owner, "thumb"]
            ],

            "entity" => [
                "definition" => [
                    "class" => fields\TextField::class,
                    "title" => Yii::t('backend', 'Entity'),
                    "showInGrid" => false,
                    "showInFilter" => true,
                    "showInForm" => false,
                ],
                "params" => [$this->owner, "entity"]
            ],
        ];
    }
}
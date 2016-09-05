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
        return [
            "name" => [
                "definition" => [
                    "class" => fields\TextField::class,
                    "title" => Yii::t('backend', 'Name'),
                    "showInGrid" => true,
                    "showInFilter" => true,
                    "isRequired" => true,
                    "editInGrid" => true,
                ],
                "params" => [$this->owner, "name"]
            ],

            "description" => [
                "definition" => [
                    "class" => fields\HtmlField::class,
                    "title" => Yii::t('backend', 'Text'),
                    "showInGrid" => false,
                    "isRequired" => true,
                ],
                "params" => [$this->owner, "description"]
            ],
        ];
    }
}
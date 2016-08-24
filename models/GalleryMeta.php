<?php
namespace lo\modules\gallery\models;

use Yii;
use lo\core\db\MetaFields;


/**
 * Class PageMeta
 * Мета описание модели страницы
 * @package app\modules\banners\models\meta
 */
class GalleryMeta extends MetaFields
{
    /**
     * @inheritdoc
     */
    protected function config()
    {
        return [
            "name" => [
                "definition" => [
                    "class" => \lo\core\db\fields\TextField::class,
                    "title" => Yii::t('common', 'Name'),
                    "showInGrid" => true,
                    "showInFilter" => true,
                    "isRequired" => true,
                    "editInGrid" => true,
                ],
                "params" => [$this->owner, "name"]
            ],
            "slug" => [
                "definition" => [
                    "class" => \lo\core\db\fields\SlugField::class,
                    "title" => Yii::t('common', 'Slug'),
                    "isRequired" => true,
                    "showInGrid" => true,
                    "generateFrom" => "name",
                ],
                "params" => [$this->owner, "slug"]
            ],
            "text" => [
                "definition" => [
                    "class" => \lo\core\db\fields\HtmlField::class,
                    "inputClass" =>[
                        'class'=>'lo\core\inputs\HtmlInput',
                        'path' => 'page',
                    ],
                    "inputClassOptions" => [
                        "widgetOptions"=>[
                            'editorOptions'=>[
                                'preset' => 'full',
                            ]
                        ],
                    ],
                    "title" => Yii::t('common', 'Text'),
                    "showInGrid" => false,
                    "isRequired" => true,
                ],
                "params" => [$this->owner, "text"]
            ],
        ];
    }
}
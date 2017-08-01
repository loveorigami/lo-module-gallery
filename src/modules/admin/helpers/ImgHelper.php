<?php

namespace lo\modules\gallery\modules\admin\helpers;

use lo\core\helpers\FA;
use lo\modules\gallery\models\GalleryItem;
use yii\helpers\Html;

/**
 * Class ImgHelper
 * @package lo\modules\gallery\modules\admin\helpers
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class ImgHelper
{
    /**
     * @param GalleryItem $model
     * @return string
     */
    public static function btnStatus(GalleryItem $model)
    {
        $icon = $model->status ? FA::_PLUS : FA::_MINUS;
        $class = $model->status ? 'btn-primary' : 'btn-danger';

        return Html::tag('span', FA::i($icon), [
            'class' => 'status-photo btn btn-xs ' . $class,
            'data' => [
                'toggle' => "tooltip",
            ],
            'title' => "статус"
        ]);
    }

    /**
     * @param GalleryItem $model
     * @return string
     */
    public static function btnOnMain(GalleryItem $model)
    {
        $icon = $model->on_main ? FA::_HOME : FA::_LIST;
        $class = $model->on_main ? 'btn-success' : 'btn-primary';

        return Html::tag('span', FA::i($icon), [
            'class' => 'onmain-photo btn btn-xs ' . $class,
            'data' => [
                'toggle' => "tooltip",
            ],
            'title' => "на главной"
        ]);
    }

    /**
     * @return string
     */
    public static function btnDelete()
    {
        $icon = FA::_REMOVE;
        $class = 'btn-primary';

        return Html::tag('span', FA::i($icon), [
            'class' => 'delete-photo btn btn-xs ' . $class,
            'data' => [
                'toggle' => "tooltip",
            ],
            'title' => "удалить"
        ]);
    }
}

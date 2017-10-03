<?php

namespace lo\modules\gallery\widgets\lightgallery;

use lo\modules\gallery\widgets\ImgById;
use yii\base\Widget;
use yii\helpers\Json;

/**
 * Class LightGalleryArticleWidget
 * @package lo\modules\gallery\widgets\lightgallery
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class LightGalleryArticleWidget extends Widget
{
    public $options = [];

    public function init()
    {
        parent::init();
        $this->registerJs();
    }

    public function registerJs()
    {
        $view = $this->getView();
        LightGalleryAsset::register($view);
        LightGalleryModeAsset::register($view);

        $this->options['selector'] = '.'.ImgById::IMG_CLASS;
        $this->options['download'] = false;
        $options = Json::encode($this->options);
        $js = '$("body").lightGallery('.$options.');';
        $view->registerJs($js);
    }
}
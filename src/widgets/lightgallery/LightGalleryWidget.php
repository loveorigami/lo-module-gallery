<?php

namespace lo\modules\gallery\widgets\lightgallery;

use yii\base\Widget;
use yii\helpers\Json;

/**
 * Class LightGalleryWidget
 * @package lo\modules\gallery\widgets\lightgallery
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class LightGalleryWidget extends Widget
{
    /**
     * Jquery selector to which element should apply the magnific-popup.
     * @var string jQuery Selector
     */
    public $target;

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

        $options = Json::encode($this->options);
        $js = '
            $("' . $this->target . '").lightGallery(' . $options . ');
            $(document).ajaxComplete(function(){
               $("' . $this->target . '").data("lightGallery").destroy(true);
               $("' . $this->target . '").lightGallery(' . $options . ');
            });
        ';

        $view->registerJs($js);
    }
}
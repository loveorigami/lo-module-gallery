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
        $this->registerCss();
    }

    public function registerJs()
    {
        $view = $this->getView();
        LightGalleryAsset::register($view);
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


    public function registerCss()
    {
        $target = $this->target;
        $css = "
            $target a img {
                padding: 4px;
                position: relative;
                cursor: pointer;
                overflow: hidden;
            }
            $target a {
                border-bottom: none;
                margin: 0 1px 1px 0;
                transition: all 0.4s ease 0.1s;
            }
            
            .gallery-img.pull-left {
                margin-right:15px;
            }
            .gallery-img.pull-right {
                margin-left:15px;
            }
        ";

        $this->getView()->registerCss($css);
    }
}
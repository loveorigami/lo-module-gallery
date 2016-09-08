<?php

namespace lo\modules\gallery\widgets;

use yii\web\AssetBundle;

class GalleryInputAsset extends AssetBundle
{

    public $js = [
        'jquery.iframe-transport.min.js',
        'jquery.gallery-manager.js',
    ];

    public $css = [
        'gallery-manager.min.css',
    ];

    public $depends = [
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__.'/assets';
    }
}

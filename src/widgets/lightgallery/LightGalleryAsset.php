<?php

namespace lo\modules\gallery\widgets\lightgallery;

use yii\web\AssetBundle;

/**
 * Class LightGalleryAsset
 * @package lo\modules\gallery\widgets\lightgallery
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class LightGalleryAsset extends AssetBundle
{
    public $sourcePath = '@bower/lightgallery';

    public $css = [
        'dist/css/lg-transitions.min.css',
        'dist/css/lightgallery.min.css'
    ];

    public $js = [
        'demo/js/lightgallery.min.js',
        'demo/js/lg-autoplay.min.js',
        'demo/js/lg-fullscreen.min.js',
        'demo/js/lg-share.min.js',
        'demo/js/lg-thumbnail.min.js',
        'demo/js/lg-video.min.js',
        'demo/js/lg-zoom.min.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
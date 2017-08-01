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
        'dist/js/lightgallery.min.js',
        'modules/lg-autoplay.min.js',
        'modules/lg-fullscreen.min.js',
        'modules/lg-share.min.js',
        'modules/lg-thumbnail.min.js',
        'modules/lg-video.min.js',
        'modules/lg-zoom.min.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
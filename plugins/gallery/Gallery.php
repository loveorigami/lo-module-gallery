<?php
namespace lo\modules\gallery\plugins\gallery;

use lo\modules\gallery\widgets\GalleryShortcode;
use lo\plugins\BaseShortcode;
use yii\web\View;

/**
 * Plugin Name: Gallery
 * Version: 1.4
 * Plugin URI:
 * Description: A simple gallery plugin for use shortcode [gallery id=1]
 * Author: Andrey Lukyanov
 * Author URI: https://github.com/loveorigami/yii2-plugins-system
 */
class Gallery extends BaseShortcode
{
    /**
     * @param $event
     */
    public static function shortcodes()
    {
        return [
            'gallery' => [
                GalleryShortcode::class, 'widget'
            ]
        ];
    }
}
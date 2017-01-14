<?php
namespace lo\modules\gallery\plugins\gallery;

use lo\modules\gallery\widgets\GalleryById;
use lo\plugins\BaseShortcode;
use yii\web\View;

/**
 * Plugin Name: Gallery
 * Version: 1.7
 * Plugin URI: https://github.com/loveorigami/lo-module-gallery/tree/master/plugins/gallery
 * Description: A simple gallery plugin for use shortcode [gallery id=1]
 * Author: Andrey Lukyanov
 * Author URI: https://github.com/loveorigami/yii2-plugins-system
 */
class GalleryShortcodes extends BaseShortcode
{
    /**
     * @param $event
     */
    public static function shortcodes()
    {
        return [
            'gallery' => [
				'callback'=> [GalleryById::class, 'widget'],
				'tooltip' => '[gallery id=1]',
				'config' => [
					'view' => 'gallery-show',
					'cols' => 6,
					'limit' => 60,
					'id' => null
				]
            ]
        ];
    }
}
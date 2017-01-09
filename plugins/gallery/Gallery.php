<?php
namespace lo\modules\gallery\plugins\gallery;

use lo\plugins\BasePlugin;
use yii\web\View;

/**
 * Plugin Name: Gallery
 * Version: 1.0
 * Plugin URI:
 * Description: A simple gallery plugin for use shortcode [gallery id=1]
 * Author: Andrey Lukyanov
 * Author URI: https://github.com/loveorigami/yii2-plugins-system
 */
class Gallery extends BasePlugin
{
    /**
     * @return array
     */
    public static function events()
    {
        return [
            View::class => [
                View::EVENT_AFTER_RENDER => ['shortcode']
            ]
        ];
    }

    /**
     * @param $event
     * @return bool
     */
    public static function shortcode($event)
    {
        if (isset($event->output)) {

            $shortcode = self::getShortcode([
                'gallery' => ['lo\modules\gallery\widgets\GalleryShortcode', 'widget']
            ]);

            $event->output = $shortcode->parse($event->output);
        }

        return null;
    }
}
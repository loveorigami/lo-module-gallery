<?php

namespace lo\modules\gallery;

use lo\modules\gallery\repository\ImageRepository;
use Yii;
use yii\base\BootstrapInterface;

/**
 * noty module bootstrap class.
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;

        $container->setSingleton(
            'lo\modules\gallery\repository\ImageGalleryInterface',
            'lo\modules\gallery\repository\ImageRepository'
        );
    }
}

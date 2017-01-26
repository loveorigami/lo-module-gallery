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
            'lo\modules\gallery\repository\ImageRepositoryInterface',
            'lo\modules\gallery\repository\ImageRepository'
        );

        // add module I18N category
        if (!isset($app->i18n->translations['gallery'])) {
            $app->i18n->translations['gallery'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@lo/modules/gallery/messages',
                'sourceLanguage' => 'en-US',
            ];
        }
    }
}

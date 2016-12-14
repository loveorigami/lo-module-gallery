<?php

return [
    'modules' => [
        'gallery' => [
            'menuItems' => [
                [
                    'label' => \Yii::t('backend', 'Gallery'),
                    'url' => ['/gallery/gallery-cat/index'],
                ],
                [
                    'label' => \Yii::t('backend', 'Images'),
                    'url' => ['/gallery/gallery-item/index'],
                ],
            ],
            'class' => 'lo\modules\gallery\modules\admin\Module',
            'controllerNamespace' => 'lo\modules\gallery\modules\admin\controllers',
            'defaultRoute' => 'gallery-cat',
        ],
    ],
];
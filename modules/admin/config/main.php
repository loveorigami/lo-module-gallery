<?php

return [
    'modules' => [
        'gallery' => [
            'class' => 'lo\modules\gallery\modules\admin\Module',
            'controllerNamespace' => 'lo\modules\gallery\modules\admin\controllers',
            'defaultRoute' => 'gallery-cat',
        ],
    ],
];
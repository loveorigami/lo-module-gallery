<?php

use modules\base\helpers\UmodeHelper;
use yii\bootstrap\Nav;

echo Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px'
    ],
    'items' => [
		[
			'label' => \Yii::t('backend', 'Gallery'),
			'url' => ['/gallery/gallery-cat/index'],
		],
		[
			'label' => \Yii::t('backend', 'Images'),
			'url' => ['/gallery/gallery-item/index'],
		],
    ]
]);
<?php
use lo\modules\gallery\widgets\lightgallery\LightGalleryWidget;
use yii\widgets\ListView;
use yii\helpers\Html;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var $viewParams
 * @var $limit
 * @var $id
 */

echo LightGalleryWidget::widget([
    'target' => '#' . $id,
    'options' => [
        'thumbnail' => true,
        'selector' => '.img-zoom',
        'download' => false,
        'zoom' => true,
        'share' => false,
        'showThumbByDefault' => false
    ],
]);

echo Html::beginTag('div', ['class' => 'row']);

echo ListView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "{items}\n<div class='clearfix'></div>{pager}",
    'itemView' => '_images',
    'emptyText' => false,
    'options' => [
        'tag' => 'div',
        'id' => $id,
        'class' => 'gallery-list'
    ],
    'viewParams' => $viewParams,
]);

echo Html::endTag('div');



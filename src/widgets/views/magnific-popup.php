<?php
use lo\widgets\magnific\MagnificPopup;
use yii\widgets\ListView;
use yii\helpers\Html;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var $viewParams
 * @var $limit
 * @var $id
 */

echo MagnificPopup::widget([
    'target' => '#' . $id,
    'options' => [
        'delegate' => 'a.img-zoom',
        'gallery' => [
            'enabled' => true
        ]
    ],
]);

echo Html::beginTag('div', ['class' => 'row']);

echo ListView::widget([
    "dataProvider" => $dataProvider,
    'layout' => "{items}\n<div class='clearfix'></div>{pager}",
    "itemView" => "_images",
    'options' => [
        'tag' => 'div',
        'id' => $id,
        'class' => 'gallery-list'
    ],
    'viewParams' => $viewParams,
]);

echo Html::endTag('div');



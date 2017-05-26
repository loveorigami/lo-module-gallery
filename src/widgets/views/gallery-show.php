<?php
use lo\widgets\magnific\MagnificPopup;
use yii\widgets\ListView;

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

echo ListView::widget([
    "dataProvider" => $dataProvider,
    "itemView" => "_images",
    'summary' => '',
    'options' => [
        'tag' => 'div',
        'id' => $id,
        'class' => 'gallery-list'
    ],
    'viewParams' => $viewParams,
]);



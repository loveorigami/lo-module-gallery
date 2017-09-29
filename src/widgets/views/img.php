<?php
/**
 * @var lo\modules\gallery\behaviors\GalleryImageBehavior $gallery
 * @var lo\modules\gallery\models\GalleryItem $model
 * @var string $pull
 * @var string $width
 */
use lo\modules\gallery\widgets\lightgallery\LightGalleryWidget;
use yii\helpers\Html;

echo LightGalleryWidget::widget([
    'target' => '.gallery-img',
    'options' => [
        'thumbnail' => true,
        'selector' => '.img-zoom',
        'download' => false,
        'zoom' => true,
        'share' => false,
        'showThumbByDefault' => false
    ],
]);

$img = $gallery->getThumbUploadUrl($model->image, $model::THUMB_TMB);
$text = Html::img($img, [
        'class' => 'img-thumbnail img-rounded img-responsive',
        'style' => $width ? 'width:' . $width . 'px;' : false,
        'title' => $model->name,
        'data' => [
            'thumb' => $img
        ]
    ]
);
?>

<div class="pull-<?= $pull ?> gallery-img">
    <?= Html::a($text, $gallery->getThumbUploadUrl($model->image, $model::THUMB_BIG), [
        'class' => 'img-zoom',
        'title' => $model->name
    ]); ?>
</div>


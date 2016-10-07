<?php
/**
 * @var lo\modules\gallery\behaviors\GalleryImageBehavior $gallery
 * @var lo\modules\gallery\models\GalleryItem $model
 * @var string $thumb
 * @var string $big
 * @var int $cols
 */

use yii\helpers\Html;

$col = 12 / $cols;
$text = Html::img($gallery->getThumbUploadUrl($model->image, $thumb), [
        'class' => 'img-thumbnail img-rounded img-responsive',
        'title' => $model->name
    ]
);
?>

<div class="col-md-<?= $col ?> col-sm-4 col-xs-6">
    <?= Html::a($text, $gallery->getThumbUploadUrl($model->image, $big), [
        'class' => 'img-zoom',
        'title' => $model->name
    ]); ?>
</div>


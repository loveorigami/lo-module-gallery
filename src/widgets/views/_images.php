<?php
/**
 * @var lo\modules\gallery\behaviors\GalleryImageBehavior $gallery
 * @var lo\modules\gallery\models\GalleryItem             $model
 * @var string                                            $thumb
 * @var string                                            $big
 * @var array                                             $thumbOptions
 * @var int                                               $cols
 */

use yii\helpers\Html;

$col = 12 / $cols;

$thOptions = \yii\helpers\ArrayHelper::merge([
    'class' => 'img-thumbnail img-rounded img-responsive',
    'title' => $model->name,
], $thumbOptions);

$text = Html::img($gallery->getThumbUploadUrl($model->image, $thumb), $thOptions);
?>

<div class="col-md-<?= $col ?> col-sm-4 col-xs-6">
    <?= Html::a($text, $gallery->getThumbUploadUrl($model->image, $big), [
        'class' => 'img-zoom',
        'title' => $model->name,
    ]); ?>
</div>


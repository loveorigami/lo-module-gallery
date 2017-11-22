<?php
/**
 * @var \lo\modules\gallery\models\GalleryItem $model
 * @var lo\modules\gallery\behaviors\GalleryImageBehavior $gallery
 * @var integer $tabindex
 */

use lo\modules\gallery\modules\admin\helpers\ImgHelper;
use yii\helpers\Html;

$text = Html::img($gallery->getThumbUploadUrl($model->image, $model::THUMB_TMB), [
        'class' => 'img-thumbnail img-rounded img-responsive',
        'title' => $model->name
    ]
);
?>

<?= Html::beginTag('div', [
    'class' => 'photo col-md-2 col-sm-6 col-xs-12',
    'data' => [
        'id' => $model->id,
        'pos' => $model->pos,
        'status' => $model->status,
        'on_main' => $model->on_main
    ]
])
?>

<div class="photo-wrap active<?= $model->status ?>">
    <div class="image-preview">
        <?= Html::a($text, $gallery->getUploadUrl($model->image), [
            'class' => 'preview-photo',
            'title' => $model->name,
            'data' => [
                'pjax' => 0
            ]
        ]); ?>
    </div>
    <div class="wrap-input">
        <input id="inptxt_<?= $model->id ?>" type="text" class="edit-photo" value="<?= $model->name ?>"
               tabindex="<?= $tabindex ?>">
    </div>
    <div class="wrap-actions">
        <div class="pull-left">
            <input tabindex="-1" type="checkbox" class="photo-select"/>
        </div>
        <div class="actions pull-right">
            <?= $model->id ?>
            <?= ImgHelper::btnOnMain($model) ?>
            <?= ImgHelper::btnStatus($model) ?>
            <?= ImgHelper::btnDelete() ?>
        </div>
    </div>
</div>

<?= Html::endTag('div') ?>

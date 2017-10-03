<?php
/**
 * @var lo\modules\gallery\behaviors\GalleryImageBehavior $gallery
 * @var lo\modules\gallery\models\GalleryItem $model
 * @var string $pull
 * @var string $img
 * @var string $width
 */
use lo\modules\gallery\models\GalleryCat;
use lo\modules\gallery\widgets\ImgById;
use yii\helpers\Html;

$src = $gallery->getThumbUploadUrl($model->image, GalleryCat::THUMB_ORI);

$text = Html::img($img, [
        'class' => 'img-thumbnail img-rounded img-responsive ',
        'style' => $width ? 'width:' . $width . 'px;' : false,
        'title' => $model->name,
        'alt' => $model->name,
        'data' => [
            'thumb' => $img,
            'src' => $src,
        ]
    ]
);
?>

<div class="<?= $pull ?>">
    <?= Html::a($text, $src, [
        'class' => ImgById::IMG_CLASS,
        'title' => $model->name,
        'data' => [
            'pinterest-text' => $model->name,
            'tweet-text' => $model->name,
        ]
    ]); ?>
</div>


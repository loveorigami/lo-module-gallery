<?php

use lo\modules\gallery\widgets\lightgallery\LightGalleryWidget;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var \lo\modules\gallery\models\GalleryItem []         $images
 * @var lo\modules\gallery\behaviors\GalleryImageBehavior $gallery
 * @var string                                            $maxFilesMsg
 */

echo LightGalleryWidget::widget([
    'target' => '#gallery-content',
    'options' => [
        'thumbnail' => false,
        'selector' => '.preview-photo',
        'download' => false,
        'zoom' => true,
        'share' => false,
        'showThumbByDefault' => false,
    ],
]);

?>
<?php echo Html::beginTag('div', $this->context->options); ?>
<!-- Gallery Toolbar -->
<div class="btn-toolbar">
    <div class="btn-group">

        <div class="btn-group">
            <label class="btn btn-default">
                <input type="checkbox" style="margin: 0 5px 0 0" class="select_all"><?php echo Yii::t(
                    'gallery',
                    'Select all'
                ); ?>
            </label>
            <label class="btn btn-default">
                <input id="to-start" name="toStart" type="checkbox" style="margin: 0 5px 0 0">
                <?php echo Yii::t('gallery', 'Add to the start'); ?>
            </label>
        </div>
        <div class="btn btn-default disabled remove-selected">
            <i class="glyphicon glyphicon-remove"></i>
        </div>
    </div>
</div>

<?= $html ?>
<?= $maxFilesMsg ?>

<!-- Gallery Photos -->
<?php Pjax::begin([
    'id' => 'gallery-content',
    'enablePushState' => false,
    'timeout' => 5000,
    'clientOptions' => [
        'showNoty' => false,
    ],
]);
?>
<div class="sorter">
    <div class="images ui-sortable">
        <?php
        $tabindex = 1;
        foreach ($images as $model) {
            echo $this->render('_image', [
                'model' => $model,
                'gallery' => $gallery,
                'tabindex' => $tabindex++,
            ]);
        }
        ?>
    </div>
</div>
<?php Pjax::end(); ?>
<?php echo Html::endTag('div'); ?>

<?php

use lo\modules\gallery\widgets\lightgallery\LightGalleryWidget;
use yii\helpers\Html;

echo LightGalleryWidget::widget([
    'target' => '#gallery',
    'options' => [
        'thumbnail' => false,
        'selector' => '.preview-photo',
        'download' => false,
        'zoom' => true,
        'share' => false,
        'showThumbByDefault' => false
    ],
]);

$this->params['loading'] = [
    'element' => '.sorter',
    'options' => [
        'stoppable' => false,
        'message' => 'Working...'
        // and more here http://carlosbonetti.github.io/jquery-loading
    ]
];

?>

<?php echo Html::beginTag('div', $this->context->options); ?>
<!-- Gallery Toolbar -->

<div class="btn-toolbar">
    <div class="btn-group">

        <div class="btn btn-success btn-file">
            <i class="glyphicon glyphicon-plus"></i><?php echo Yii::t('gallery', 'Add…'); ?>
            <?= $html; ?>
        </div>

        <div class="btn-group">
            <label class="btn btn-default">
                <input type="checkbox" style="margin: 0 5px 0 0" class="select_all"><?php echo Yii::t(
                    'gallery',
                    'Select all'
                ); ?>
            </label>
            <label class="btn btn-default">
                <input type="checkbox" style="margin: 0 5px 0 0" class="add_start"><?php echo Yii::t(
                    'gallery',
                    'Add to the start'
                ); ?>
            </label>
        </div>

        <div class="btn btn-default disabled edit-selected">
            <i class="glyphicon glyphicon-pencil"></i>
        </div>
        <div class="btn btn-default disabled remove-selected">
            <i class="glyphicon glyphicon-remove"></i>
        </div>
    </div>
</div>

<hr/>
<div class="log"></div>

<!-- Gallery Photos -->
<div class="sorter">
    <div class="images"></div>
    <div class="clearfix"></div>
</div>

<!-- Modal window to edit photo information -->
<div class="editor-modal modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">×</a>

                <h3 class="modal-title"><?php echo Yii::t('gallery', 'Edit information') ?></h3>
            </div>
            <div class="modal-body">
                <div class="form"></div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary save-changes">
                    <?php echo Yii::t('gallery', 'Save changes') ?>
                </a>
                <a href="#" class="btn" data-dismiss="modal"><?php echo Yii::t(
                        'gallery',
                        'Close'
                    ) ?></a>
            </div>
        </div>
    </div>
</div>

<div class="overlay">
    <div class="overlay-bg">&nbsp;</div>
    <div class="drop-hint">
        <span class="drop-hint-info"><?php echo Yii::t('gallery', 'Drop Files Here…') ?></span>
    </div>
</div>
<div class="progress-overlay">
    <div class="overlay-bg">&nbsp;</div>
    <!-- Upload Progress Modal-->
    <div class="modal progress-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><?php echo Yii::t('gallery', 'Uploading images…') ?></h3>
                </div>
                <div class="modal-body">
                    <div class="progress ">
                        <div class="progress-bar progress-bar-info progress-bar-striped active upload-progress"
                             role="progressbar">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo Html::endTag('div'); ?>

<?php
/**
 * @var yii\web\View $this
 * @var lo\modules\gallery\models\GalleryItem $model
 */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Image',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Images'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-create">
    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>
</div>

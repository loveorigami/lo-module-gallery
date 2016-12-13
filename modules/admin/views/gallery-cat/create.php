<?php
/**
 * @var yii\web\View $this
 * @var lo\modules\gallery\models\GalleryCat $model
 */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Gallery',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Gallery'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-create">
    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>
</div>

<?php

/**
 * @var yii\web\View $this
 * @var lo\modules\gallery\models\GalleryCat $model
 */

$this->title = Yii::t('backend', 'Update') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Images'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="page-update">
    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

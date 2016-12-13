<?php
use lo\core\widgets\admin\Detail;
use lo\core\widgets\admin\CrudLinks;

/**
 * @var yii\web\View $this
 * @var lo\modules\gallery\models\GalleryCat $model
 */

$this->title = Yii::t('backend', 'View');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Gallery'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-view">
    <?= CrudLinks::widget(["action" => CrudLinks::CRUD_VIEW, "model" => $model]) ?>
    <?= Detail::widget([
        'model' => $model
    ]) ?>
</div>
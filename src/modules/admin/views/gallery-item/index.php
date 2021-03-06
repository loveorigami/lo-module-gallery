<?php
use lo\core\widgets\admin\Grid;
use lo\core\widgets\admin\CrudLinks;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lo\modules\gallery\models\GalleryItem $searchModel
 */

$this->title = Yii::t('backend', 'Images');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">
    <?php
    echo CrudLinks::widget(["action" => CrudLinks::CRUD_LIST, "model" => $searchModel]);
    echo $this->render('/_menu');
    echo $this->render('_filter', ['model' => $searchModel]);
    echo Grid::widget([
        'dataProvider' => $dataProvider,
        'model' => $searchModel,
    ]);
    ?>
</div>

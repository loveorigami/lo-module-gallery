<?php
use lo\core\widgets\admin\ExtFilter;

/**
 * @var yii\web\View $this
 * @var lo\modules\gallery\models\GalleryCat $model
 */

echo ExtFilter::widget(["model" => $model]);
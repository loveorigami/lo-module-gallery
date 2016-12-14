<?php
use lo\core\widgets\admin\ExtFilter;

/**
 * @var yii\web\View $this
 * @var lo\modules\gallery\models\GalleryItem $model
 */

echo ExtFilter::widget(["model" => $model]);
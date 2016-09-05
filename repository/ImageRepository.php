<?php

namespace lo\modules\gallery\repository;

use lo\core\db\ActiveRecord;
use yii\base\Object;

/**
 * Class ImageRepository
 * @package lo\modules\gallery\behaviors
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class ImageRepository extends Object implements ImageRepositoryInterface
{
    /** @var string */
    public $modelName;

    /** @var ActiveRecord $model */
    protected $model;

    public function loadModel($id = null)
    {
        if (!$id) {
            return $this->model = new $this->modelName;
        } else {
            return $this->model;
        }
    }


    public function save()
    {
        if (!$this->model->save()) {
            echo 111;
        };
    }
}

<?php

namespace lo\modules\gallery\repository;

use lo\core\db\ActiveRecord;
use yii\base\Model;
use yii\base\Object;
use yii\web\NotFoundHttpException;

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
        if (!$this->model) {
            if (!$id) {
                return $this->model = new $this->modelName;
            } else {
                return $this->model = $this->findModel($id);
            }
        }
        return $this->model;
    }


    public function save()
    {
        if (!$this->model->save()) {
            print_r($this->model->errors);
        }
    }

    /**
     * @param integer $id
     * @return Model $model
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        /** @var ActiveRecord $class */
        $class = $this->modelName;
        if (($model = $class::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

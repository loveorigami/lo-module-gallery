<?php

namespace lo\modules\gallery\repository;

use lo\core\db\ActiveQuery;
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
    protected $modelClass;

    /** @var string */
    protected $entity;

    /** @var ActiveRecord $owner */
    protected $owner;

    /** @var ActiveRecord $model */
    protected $model;

    public function loadModel($id = null)
    {
        if (!$this->model) {
            if (!$id) {
                return $this->model = new $this->modelClass;
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
        $class = $this->modelClass;
        if (($model = $class::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @param $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param $modelClass
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return ActiveQuery relation
     */
    public function getImages()
    {
        $query =  new ActiveQuery($this->modelClass);

/*

            $this->owner->hasMany($this->modelClass, ['owner_id' => 'id'])
            ->andWhere(['entity' => $this->entity])
            ->addOrderBy(['pos' => SORT_DESC]);*/

        $query->published();

        return $query;
    }
}

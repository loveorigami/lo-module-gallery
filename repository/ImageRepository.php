<?php

namespace lo\modules\gallery\repository;

use lo\core\db\ActiveQuery;
use lo\core\db\ActiveRecord;
use lo\modules\gallery\models\GalleryItem;
use Yii;
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
    public $modelClass;

    /** @var string */
    public $entity;

    /** @var string */
    protected $ownerId;

    /** @var ActiveRecord $model */
    protected $model;

    public function save()
    {
        if (!$this->model->save()) {
            print_r($this->model->errors);
        }
    }


    public function delete(){
        $this->model->delete();
    }

    /**
     * @return ActiveRecord
     */
    public function getModel(){
        return $this->model;
    }

    /**
     * @param null $id
     * @return ActiveRecord|Model
     */
    public function setModel($id = null)
    {
        if (!$id) {
            return $this->model = new $this->modelClass;
        } else {
            return $this->model = $this->findModel($id);
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
     * @param $ownerId
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * @return ActiveQuery relation
     */
    public function getImages()
    {
        $model = $this->modelClass;
        /** @var ActiveRecord $model */
        $query = $model::find();

        $query->select('*')
            ->where([
                'entity' => $this->entity,
                'owner_id' => $this->ownerId
            ])
            ->orderBy(['pos' => SORT_ASC]);

        return $query;
    }

    /**
     * @param array $order
     * @return array
     */
    public function reOrder($order)
    {
        /** @var GalleryItem $model */
        $model = $this->modelClass;

        $orders = [];
        $i = 0;

        foreach ($order as $k => $v) {
            if (!$v) {
                $order[$k] = $k;
            }
            $orders[] = $order[$k];
            $i++;
        }

        sort($orders);

        $i = 0;
        $res = [];

        foreach ($order as $k => $v) {
            $res[$k] = $orders[$i];

            Yii::$app->db->createCommand()
                ->update(
                    $model::tableName(),
                    ['pos' => $orders[$i]],
                    ['id' => $k]
                )->execute();

            $i++;
        }

        return $order;
    }
}

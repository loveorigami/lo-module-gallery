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

    /** @var GalleryItem $model */
    protected $model;

    /**
     * save item
     */
    public function save()
    {
        if (!$this->model->save()) {
            print_r($this->model->errors);
        }
        return true;
    }

    /**
     * delete item
     */
    public function delete()
    {
        $this->model->delete();
    }

    /**
     * @return ActiveRecord
     */
    public function getModel()
    {
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
     * @return boolean
     */
    public function getDefaultStatus()
    {
        $model = $this->model;
        return $model::STATUS_PUBLISHED;
    }

    /**
     * @param array $ids
     * @return ActiveQuery relation
     */
    public function getImages($ids = [])
    {
        $model = $this->modelClass;
        /** @var GalleryItem $model */
        $query = $model::find();

        $query->select('*')
            ->where([
                'entity' => $this->entity,
                'owner_id' => $this->ownerId
            ])
            ->orderBy(['pos' => SORT_ASC]);

        if ($ids) {
            $query->andWhere(['in', 'id', $ids]);
        }

        return $query;
    }

    /**
     * @param GalleryItem $model
     * @return array
     */
    public function getImagePathInfo($model)
    {
        if (!$model) return null;

        $file = pathinfo($model->image);

        if ($model->name) {
            $file['filename'] = $model->name . '.' . $file['extension'];
        } else {
            $file['basename'] = null;
        }
        return $file;
    }

    /**
     * @param array $order
     * @return array
     */
    public function reOrder($order)
    {
        /** @var GalleryItem $model */
        $model = $this->modelClass;

        foreach ($order as $pos => $id) {

            Yii::$app->db->createCommand()
                ->update(
                    $model::tableName(),
                    ['pos' => $pos],
                    ['id' => $id]
                )->execute();
        }

        return $order;
    }

    /**
     * @param $imagesData
     * @return array
     */
    public function updateData($imagesData)
    {
        /** @var GalleryItem $model */
        $model = $this->modelClass;

        $imageIds = array_keys($imagesData);

        /** @var GalleryItem[] $imagesToUpdate */
        $imagesToUpdate = $this->getImages($imageIds)->all();

        foreach ($imagesToUpdate as $image) {

            if (isset($imagesData[$image->id]['name'])) {
                $image->name = $imagesData[$image->id]['name'];
            }
            if (isset($imagesData[$image->id]['description'])) {
                $image->description = $imagesData[$image->id]['description'];
            }

            Yii::$app->db->createCommand()
                ->update(
                    $model::tableName(),
                    ['name' => $image->name, 'description' => $image->description],
                    ['id' => $image->id]
                )->execute();
        }

        return $imagesToUpdate;
    }
}

<?php

namespace lo\modules\gallery\repository;

use lo\core\db\ActiveQuery;
use lo\core\db\ActiveRecord;
use lo\core\helpers\ArrayHelper;
use lo\modules\gallery\models\GalleryItem;
use Yii;
use yii\base\InvalidParamException;
use yii\base\Object;

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
    public $ownerId;

    /** @var GalleryItem $model */
    protected $model;

    /**
     * @return GalleryItem
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new $this->modelClass;
            $this->model->entity = $this->entity;
            $this->model->owner_id = $this->ownerId;
        }

        return $this->model;
    }

    /**
     * @param GalleryItem $model
     */
    public function setModel($model)
    {
        if (!($model instanceof $this->modelClass)) {
            throw new InvalidParamException('Model must be instanceof ' . $this->modelClass);
        }
        $this->model = $model;
    }

    /**
     * @param $data
     * @return bool
     */
    public function saveImage($data)
    {
        $image = $this->loadModel($data);
        return $image->save();
    }

    /**
     * @return bool
     */
    public function deleteImage()
    {
        return $this->model->delete();
    }

    /**
     * @param $data
     * @return ActiveRecord
     */
    protected function loadModel($data)
    {
        $model = $this->getModel();

        $scenario = $name = ArrayHelper::getValue($data, 'scenario', $model::SCENARIO_UPDATE);
        $model->scenario = $scenario;

        if ($scenario == $model::SCENARIO_INSERT) {
            $model->entity = $this->entity;
            $model->status = $model::STATUS_PUBLISHED;
            $model->pos = $this->findImages()->count() + 1;
        }

        $name = ArrayHelper::getValue($data, 'name');
        if ($name) $model->name = $name;

        $image = ArrayHelper::getValue($data, 'image');
        if ($image) $model->image = $image;

        $path = ArrayHelper::getValue($data, 'path');
        if ($path) $model->path = $path;

        return $model;
    }

    /**
     * @return array
     */
    public function getImagePathInfo()
    {
        if (!$this->model) return null;

        $file = pathinfo($this->model->image);

        if ($this->model->name) {
            $file['filename'] = $this->model->name . '.' . $file['extension'];
        } else {
            $file['basename'] = null;
        }
        return $file;
    }

    /**
     * @return string
     */
    public function getImageFile()
    {
        return $this->model->image;
    }

    /**
     * @return string
     */
    public function oldImage()
    {
        $old = $this->model->getOldAttributes();
        return ($old['name'] != $this->model->name) ? $old['image'] : false;
    }


    /**
     * @param array $ids
     * @return ActiveQuery relation
     */
    public function findImages($ids = [])
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
     * @param $data
     * @return array
     */
    public function updateImages($data)
    {
        /** @var GalleryItem $model */
        $model = $this->modelClass;

        $imageIds = array_keys($data);

        /** @var GalleryItem[] $imagesToUpdate */
        $imagesToUpdate = $this->findImages($imageIds)->all();

        foreach ($imagesToUpdate as $image) {

            if (isset($data[$image->id]['name'])) {
                $image->name = $data[$image->id]['name'];
            }
            if (isset($data[$image->id]['description'])) {
                $image->description = $data[$image->id]['description'];
            }
            if (isset($data[$image->id]['status'])) {
                $image->status = $data[$image->id]['status'] ?
                    $model::STATUS_DRAFT :
                    $model::STATUS_PUBLISHED;
            }
            if (isset($data[$image->id]['on_main'])) {
                $image->on_main = $data[$image->id]['on_main'] ?
                    $model::STATUS_DRAFT :
                    $model::STATUS_PUBLISHED;
            }

            Yii::$app->db->createCommand()
                ->update(
                    $model::tableName(),
                    [
                        'name' => $image->name,
                        'description' => $image->description,
                        'status' => $image->status,
                        'on_main' => $image->on_main
                    ],
                    ['id' => $image->id]
                )->execute();
        }

        return $imagesToUpdate;
    }

    /**
     * @param array $ids
     * @return array
     */
    public function reOrder($ids)
    {
        /** @var GalleryItem $model */
        $model = $this->modelClass;

        foreach ($ids as $pos => $id) {

            Yii::$app->db->createCommand()
                ->update(
                    $model::tableName(),
                    ['pos' => $pos],
                    ['id' => $id]
                )->execute();
        }

        return $ids;
    }
}

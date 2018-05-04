<?php

namespace lo\modules\gallery\repository;

use lo\core\db\ActiveQuery;
use lo\core\db\ActiveRecord;
use lo\core\helpers\ArrayHelper;
use lo\modules\gallery\models\GalleryItem;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\BaseObject;

/**
 * Class ImageRepository
 * @package lo\modules\gallery\behaviors
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 *
 * @property array $imagePathInfo
 * @property string $imageFile
 */
class ImageRepository extends BaseObject implements ImageRepositoryInterface
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
            throw new InvalidArgumentException('Model must be instanceof ' . $this->modelClass);
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
        $this->model = null;
        return $image->save();
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
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

        $scenario = ArrayHelper::getValue($data, 'scenario', $model::SCENARIO_UPDATE);
        $model->scenario = $scenario;

        if ($scenario == $model::SCENARIO_INSERT) {
            $toStart = ArrayHelper::getValue($data, 'toStart', 0);
            $pos = $this->findImages()->count() + 1;

            $model->pos = $toStart ? -$pos : $pos;
            $model->entity = $this->entity;
            $model->status = $model::STATUS_PUBLISHED;
        }

        $name = ArrayHelper::getValue($data, 'name');
        $model->name = $name;

        $image = ArrayHelper::getValue($data, 'image');
        $model->image = $image;

        $path = ArrayHelper::getValue($data, 'path');
        $model->path = $path;

        $thumb = ArrayHelper::getValue($data, 'thumb');
        $model->thumb = $thumb;

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
     * @var ActiveRecord $model
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
     * @return int|string
     */
    public function count()
    {
        return $this->findImages()->count();
    }

    /**
     * @param $data
     * @return ActiveRecord
     */
    public function updateImage($data)
    {
        /** @var GalleryItem $model */
        $model = $this->modelClass;

        $imageIds = array_keys($data);

        /** @var GalleryItem $image */
        $image = $this->findImages($imageIds)->one();

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

        $this->setModel(clone $image);
        $image->save();

        return $image;
    }

    /**
     * @param array $ids
     * @return array
     * @throws \yii\db\Exception
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

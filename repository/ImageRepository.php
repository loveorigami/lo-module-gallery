<?php

namespace lo\modules\gallery\repository;

use lo\core\db\ActiveQuery;
use lo\core\db\ActiveRecord;
use lo\core\helpers\ArrayHelper;
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
    public $ownerId;

    /** @var GalleryItem $model */
    protected $model;

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

    /**
     * @param $data
     * @param null $model
     * @return bool
     */
    public function saveImage($data, $model = null)
    {
        $image = $this->loadModel($data, $model);
        return $image->save();
    }

    /**
     * @param $imagesData
     * @return array
     */
    public function updateImages($imagesData)
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

    /**
     * @param $data
     * @param null $model
     * @return ActiveRecord
     */
    protected function loadModel($data, $model = null)
    {
        if (!$model or !($model instanceof GalleryItem)) {
            $id = ArrayHelper::getValue($data, 'id');
            $model = $this->setModel($id);
        }

        $scenario = $name = ArrayHelper::getValue($data, 'scenario', $model::SCENARIO_UPDATE);
        $model->scenario = $scenario;

        if ($scenario == $model::SCENARIO_INSERT) {
            $model->entity = $this->entity;
            $model->status = $model::STATUS_PUBLISHED;
            $model->pos = $this->getImages()->count() + 1;
        }

        $name = ArrayHelper::getValue($data, 'name');
        if ($name) $model->name = $name;

        $image = ArrayHelper::getValue($data, 'image');
        if ($image) $model->image = $image;

        $path = ArrayHelper::getValue($data, 'path');
        if ($path) $model->path = $path;

        $owner_id = ArrayHelper::getValue($data, 'owner_id');
        if ($owner_id) $model->owner_id = $owner_id;

        return $model;
    }

    /**
     * @param ActiveRecord $model
     * @return string
     */
    public
    function oldImage($model)
    {
        $old = $model->getOldAttributes();
        return ($old['name'] != $model->name) ? $old['image'] : false;
    }
}

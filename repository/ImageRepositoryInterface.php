<?php
/**
 * Created by PhpStorm.
 * User: Lukyanov Andrey <loveorigami@mail.ru>
 * Date: 05.09.2016
 * Time: 10:40
 */
namespace lo\modules\gallery\repository;
use lo\core\db\ActiveRecord;

/**
 * Interface ImageRepositoryInterface
 * @package lo\modules\gallery\repository
 * @property string $modelClass
 * @property string $entity
 * @property string $ownerId
 * @property string $image
 */
interface ImageRepositoryInterface
{
    /**
     * @return \lo\core\db\ActiveRecord
     */
    public function getModel();

    /**
     * query find()
     * @param array $ids
     * @return \lo\core\db\ActiveQuery
     */
    public function getImages($ids = []);

    /**
     * @param $imagesData
     * @return array
     */
    public function updateImages($imagesData);

    /**
     * @param $data
     * @param ActiveRecord $model
     * @return mixed
     */
    public function saveImage($data, $model = null);

    /**
     * @param \lo\core\db\ActiveRecord $model
     * @return array
     */
    public function getImagePathInfo($model);

    /**
     * Сортировка
     * @param array $ids
     * @return array
     */
    public function reOrder($ids);

    /**
     * @param \lo\core\db\ActiveRecord $model
     * @return string
     */
    public function oldImage($model);

}
<?php
/**
 * Created by PhpStorm.
 * User: Lukyanov Andrey <loveorigami@mail.ru>
 * Date: 05.09.2016
 * Time: 10:40
 */
namespace lo\modules\gallery\repository;

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
     * @param \lo\core\db\ActiveRecord $model
     */
    public function setModel($model);

    /**
     * @param $data
     * @return mixed
     */
    public function saveImage($data);

    /**
     * @return bool
     */
    public function deleteImage();

    /**
     * @return array
     */
    public function getImagePathInfo();

    /**
     * @return string
     */
    public function getImageFile();

    /**
     * @return string
     */
    public function oldImage();

    /**
     * query find()
     * @param array $ids
     * @return \lo\core\db\ActiveQuery
     */
    public function findImages($ids = []);

    /**
     * @param array $data
     * @return \lo\core\db\ActiveRecord
     */
    public function updateImage($data);

    /**
     * Сортировка
     * @param array $ids
     * @return array
     */
    public function reOrder($ids);



}
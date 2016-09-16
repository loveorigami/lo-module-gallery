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
 */
interface ImageRepositoryInterface
{
    /**
     * @param $id
     * @return \yii\base\Model
     */
    public function setModel($id = null);

    /**
     * @return \yii\base\Model
     */
    public function getModel();

    /**
     * query find()
     * @param array $ids
     * @return \lo\core\db\ActiveQuery
     */
    public function getImages($ids = []);

    /**
     * @param ActiveRecord $model
     * @return array
     */
    public function getImagePathInfo($model);

    /**
     * Сортировка
     * @param array $order
     * @return array
     */
    public function reOrder($order);

    /**
     * @param $imagesData
     * @return array
     */
    public function updateImages($imagesData);

    public function saveImage($data);
}
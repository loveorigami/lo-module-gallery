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
 * @const STATUS_PUBLISHED
 * @property integer $id
 * @property string $name
 * @property string $entity
 * @property string $image
 * @property string $owner_id
 * @property string $description
 * @property string $path
 * @property integer $status
 * @property integer $pos
 * @property integer $created_at
 * @property integer $updated_at
 */
interface ImageRepositoryInterface
{
    /** @param $ownerId */
    public function setOwnerId($ownerId);

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
     * @return \lo\core\db\ActiveQuery
     */
    public function getImages();

    /**
     * @return boolean
     */
    public function getDefaultStatus();

    /**
     * Сортировка
     * @param array $order
     * @return array
     */
    public function reOrder($order);

    /**
     * удаление
     * @return boolean
     */
    public function delete();

    public function save();

    /**
     * @param $imagesData
     * @return array
     */
    public function updateData($imagesData);
}
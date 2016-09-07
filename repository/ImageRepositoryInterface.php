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
 * @param $id
 * @param image
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
}
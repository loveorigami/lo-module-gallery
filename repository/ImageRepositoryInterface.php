<?php
/**
 * Created by PhpStorm.
 * User: Lukyanov Andrey <loveorigami@mail.ru>
 * Date: 05.09.2016
 * Time: 10:40
 */
namespace lo\modules\gallery\repository;

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

    public function getImages();

    public function save();
}
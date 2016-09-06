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
    /** @param $owner */
    public function setOwner($owner);

    /** @param $entity */
    public function setEntity($entity);

    /** @param $modelClass */
    public function setModelClass($modelClass);

    public function loadModel($id = null);

    public function getImages();

    public function save();
}
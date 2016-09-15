<?php

namespace lo\modules\gallery\behaviors;

use abeautifulsite\SimpleImage;
use Exception;
use lo\core\db\ActiveQuery;
use lo\core\db\ActiveRecord;
use lo\modules\gallery\repository\ImageRepositoryInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;


/**
 * Behavior for adding gallery to any model.
 * @property ActiveRecord $owner
 */
class GalleryImageBehavior extends GalleryBehavior
{
    /** @var string */
    public $placeholder;

    /** @var boolean */
    public $createThumbsOnSave = true;

    /** @var boolean */
    public $createThumbsOnRequest = false;

    /**
     * @var array the thumbnail profiles
     * - `width`
     * - `height`
     * - `quality`
     */
    public $thumbs = [
        'tmb' => ['width' => 200, 'height' => 200, 'quality' => 90],
    ];

    /** @var string|null */
    public $thumbPath;

    /** @var string|null */
    public $thumbUrl;

    /** @var string Type name assigned to model in image attachment action */
    public $entity;

    /** @var string Class name gallery */
    public $modelClass;

    /** @var ImageRepositoryInterface $_repository */
    protected $_repository;

    /**
     * @inheritdoc
     */
    public function __construct(ImageRepositoryInterface $repository, array $config = [])
    {
        parent::__construct($config);

        if ($this->modelClass === null) {
            throw new InvalidConfigException('The "modelClass" property must be set.');
        }

        $this->_repository = new $repository([
            'modelClass' => $this->modelClass,
            'entity' => $this->entity
        ]);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->createThumbsOnSave) {
            if ($this->thumbPath === null) {
                $this->thumbPath = $this->path;
            }
            if ($this->thumbUrl === null) {
                $this->thumbUrl = $this->url;
            }

            foreach ($this->thumbs as $config) {
                $width = ArrayHelper::getValue($config, 'width');
                $height = ArrayHelper::getValue($config, 'height');
                if ($height < 1 && $width < 1) {
                    throw new InvalidConfigException(sprintf(
                        'Length of either side of thumb cannot be 0 or negative, current size ' .
                        'is %sx%s', $width, $height
                    ));
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * @param array $ids
     * @return ActiveQuery relation
     */
    public function getImages($ids = [])
    {
        $this->_repository->setOwnerId($this->getOwnerId());
        return $this->_repository->getImages($ids);
    }

    /**
     * @param null $id
     * @return Model
     */
    public function setModel($id = null)
    {
        return $this->_repository->setModel($id);
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->_repository->getModel();
    }

    /**
     * @param array $order
     * @return array
     */
    public function reOrder($order)
    {
        return $this->_repository->reOrder($order);
    }


    /**
     * @inheritdoc
     */
    protected function afterUpload()
    {
        parent::afterUpload();

        if ($this->createThumbsOnSave) {
            $this->createThumbs();
        }

        /**
         * @var ImageRepositoryInterface $model
         */
        $model = $this->_repository->setModel();
        $model->name = $this->getOriginalFileName();

        $model->image = $this->fileName;
        $model->entity = $this->entity;
        $model->owner_id = $this->getOwnerId();
        $model->status = $this->_repository->getDefaultStatus();
        $model->path = $this->resolvePath($this->path);
        $this->_repository->save();
        $model->pos = $model->id;
        $this->_repository->save();

    }

    /**
     * @param array $imagesData
     * @return array
     */
    public function updateImagesData($imagesData)
    {
        $this->_repository->setOwnerId($this->getOwnerId());
        $updateData = $this->_repository->updateData($imagesData);
        $this->renameImages($updateData);
        return $updateData;
    }

    /**
     * Get Gallery Id
     * @return mixed as string or integer
     * @throws Exception
     */
    protected function getOwnerId()
    {
        $pk = $this->owner->getPrimaryKey();
        if (is_array($pk)) {
            throw new Exception('Composite pk not supported');
        } else {
            return $pk;
        }
    }

    /**
     * @throws InvalidParamException
     */
    protected function createThumbs()
    {
        $filename = $this->fileName;
        $path = $this->getUploadPath($filename);

        foreach ($this->thumbs as $profile => $config) {
            $thumbPath = $this->getThumbUploadPath($filename, $profile);
            if ($thumbPath !== null) {
                if (!FileHelper::createDirectory(dirname($thumbPath))) {
                    throw new InvalidParamException("Directory specified in 'thumbPath' attribute doesn't exist or cannot be created.");
                }
                if (!is_file($thumbPath)) {
                    $this->generateImageThumb($config, $path, $thumbPath);
                }
            }
        }
    }

    /**
     * @param string $filename
     * @param string $profile
     * @return string
     */
    public function getThumbUploadPath($filename, $profile = 'tmb')
    {
        $path = $this->resolvePath($this->thumbPath);
        $filename = $this->getThumbFileName($filename, $profile);

        return $filename ? Yii::getAlias($path . '/' . $filename) : null;
    }

    /**
     * @param string $filename
     * @param string $profile
     * @return string|null
     */
    public function getThumbUploadUrl($filename, $profile = 'tmb')
    {
        $path = $this->getUploadPath($filename);

        if (is_file($path)) {
            if ($this->createThumbsOnRequest) {
                $this->setFileName($filename);
                $this->createThumbs();
            }

            $url = $this->resolvePath($this->thumbUrl);

            $thumbName = $this->getThumbFileName($filename, $profile);

            return Yii::getAlias($url . '/' . $thumbName);
        } elseif ($this->placeholder) {
            return $this->getPlaceholderUrl($profile);
        } else {
            return null;
        }
    }

    /**
     * @param $profile
     * @return string
     */
    protected function getPlaceholderUrl($profile)
    {
        list ($path, $url) = Yii::$app->assetManager->publish($this->placeholder);
        $filename = basename($path);
        $thumb = $this->getThumbFileName($filename, $profile);
        $thumbPath = dirname($path) . DIRECTORY_SEPARATOR . $thumb;
        $thumbUrl = dirname($url) . '/' . $thumb;

        if (!is_file($thumbPath)) {
            $this->generateImageThumb($this->thumbs[$profile], $path, $thumbPath);
        }

        return $thumbUrl;
    }

    /**
     * event after delete
     */
    public function afterDelete()
    {
        $this->deleteAll();
    }

    /**
     * delete all images
     * @param array $ids
     */
    public function deleteAll($ids = [])
    {
        /** @var ImageRepositoryInterface[] $images */
        $images = $this->getImages($ids)->all();

        foreach ($images as $model) {
            $this->delete($model->image);
            $model->delete();
        }
    }

    /**
     * delete file
     * @param string $filename
     */
    protected function delete($filename)
    {
        parent::delete($filename);
        $this->deleteThumbs($filename);
    }

    /**
     * delete thumbs
     * @param $filename
     */
    protected function deleteThumbs($filename)
    {
        $profiles = array_keys($this->thumbs);
        foreach ($profiles as $profile) {
            $path = $this->getThumbUploadPath($filename, $profile);
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    /**
     * @param $model
     * @return string
     */
    protected function getImagePathInfo($model)
    {
        return $this->_repository->getImagePathInfo($model);
    }

    /**
     * @param ImageRepositoryInterface[] $data
     */
    protected function renameImages($data)
    {
        foreach ($data as $img) {
            /** @var ActiveRecord $img */
            $old = $img->getOldAttributes();
            if ($old['name'] != $img->name) {
                $old_filename = $old['image'];

                $fileinfo = $this->getImagePathInfo($img);
                $new_filename = $this->getFileName($fileinfo);

                $img->image = $new_filename;

                if ($img->save()) {
                    $this->deleteThumbs($old_filename);
                    $this->renameFile($old_filename, $new_filename);
                };
            }
        }
    }

    /**
     * @param string $filename
     * @param string $profile
     * @return string
     */
    protected function getThumbFileName($filename, $profile = 'tmb')
    {
        return $profile . '-' . $filename;
    }

    /**
     * @param $config
     * @param $path
     * @param $thumbPath
     */
    protected function generateImageThumb($config, $path, $thumbPath)
    {
        $width = ArrayHelper::getValue($config, 'width');
        $height = ArrayHelper::getValue($config, 'height');
        $quality = ArrayHelper::getValue($config, 'quality', 100);

        $img = new SimpleImage($path);
        $img->thumbnail($width, $height)->save($thumbPath, $quality);
    }

}

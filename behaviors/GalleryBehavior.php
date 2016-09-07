<?php

namespace lo\modules\gallery\behaviors;

use Closure;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;


/**
 * Behavior for adding gallery to any model.
 * @property string $galleryId
 */
class GalleryBehavior extends Behavior
{
    /** @event Event an event that is triggered after a file is uploaded. */
    const EVENT_AFTER_UPLOAD = 'afterUpload';

    /** @var string the attribute which holds the attachment. */
    public $attribute;

    /** @var array the scenarios in which the behavior will be triggered */
    public $scenarios = [];

    /** @var string the base path or path alias to the directory in which to save files. */
    public $path;

    /** @var string the base URL or path alias for this file */
    public $url;

    /** @var string name this file */
    public $fileName;

    /** @var string original name this file */
    public $originalFileName;

    /** @var bool Getting file instance by name */
    public $instanceByName = true;

    /**
     * @var boolean|callable generate a new unique name for the file
     * set true or anonymous function takes the old filename and returns a new name.
     * @see self::generateFileName()
     */
    public $generateNewName = true;

    /** @var boolean If `true` current attribute file will be deleted */
    public $unlinkOnSave = true;

    /** @var boolean If `true` current attribute file will be deleted after model deletion. */
    public $unlinkOnDelete = true;

    /** @var boolean $deleteTempFile whether to delete the temporary file after saving. */
    public $deleteTempFile = true;

    /** @var UploadedFile the uploaded file instance. */
    private $_file;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->attribute === null) {
            throw new InvalidConfigException('The "attribute" property must be set.');
        }
        if ($this->path === null) {
            throw new InvalidConfigException('The "path" property must be set.');
        }
        if ($this->url === null) {
            throw new InvalidConfigException('The "url" property must be set.');
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            //BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            //BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            //BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            //BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * This method is invoked before validation starts.
     */
    public function uploadFile()
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;

        if (in_array($model->scenario, $this->scenarios)) {

            $this->_file = UploadedFile::getInstance($model, $this->attribute);

            if ($this->_file instanceof UploadedFile) {
                $this->originalFileName = $this->_file->baseName;
                $this->_file->name = $this->getFileName($this->_file);
                $this->fileName = $this->_file->name;

                $path = $this->getUploadPath($this->fileName);
                if (is_string($path) && FileHelper::createDirectory(dirname($path))) {
                    $this->save($this->_file, $path);
                    $this->afterUpload();
                } else {
                    throw new InvalidParamException("Directory specified in 'path' attribute doesn't exist or cannot be created.");
                }
            }
        }
    }

    /**
     * Returns file path for the filename.
     * @param string $filename
     * @return string|null the file path.
     */
    public function getUploadPath($filename)
    {
        $path = $this->resolvePath($this->path);
        return $filename ? Yii::getAlias($path . '/' . $filename) : null;
    }

    /**
     * Returns file path for the filename.
     * @return string|null the file path.
     */
    public function getPath()
    {
        $path = $this->resolvePath($this->path);
        return $path ? Yii::getAlias($path) : null;
    }

    /**
     * Returns file url for the filename.
     * @param string $filename
     * @return string|null
     */
    public function getUploadUrl($filename)
    {
        $url = $this->resolvePath($this->url);
        return $filename ? Yii::getAlias($url . '/' . $filename) : null;
    }

    /**
     * Replaces all placeholders in path variable with corresponding values.
     * @param $path
     * @return mixed
     */
    protected function resolvePath($path)
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        return preg_replace_callback('/{([^}]+)}/', function ($matches) use ($model) {
            $name = $matches[1];
            $attribute = ArrayHelper::getValue($model, $name);
            if (is_string($attribute) || is_numeric($attribute)) {
                return $attribute;
            } else {
                return $matches[0];
            }
        }, $path);
    }

    /**
     * Saves the uploaded file.
     * @param UploadedFile $file the uploaded file instance
     * @param string $path the file path used to save the uploaded file
     * @return boolean true whether the file is saved successfully
     */
    protected function save($file, $path)
    {
        return $file->saveAs($path, $this->deleteTempFile);
    }

    /**
     * Deletes old file.
     * @param string $filename
     */
    protected function delete($filename)
    {
        $path = $this->getUploadPath($filename);
        if (is_file($path)) {
            unlink($path);
        }
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    protected function getFileName($file)
    {
        if ($this->generateNewName) {
            return $this->generateNewName instanceof Closure
                ? call_user_func($this->generateNewName, $file)
                : $this->generateFileName($file);
        } else {
            return $this->sanitize($file->name);
        }
    }

    /**
     * @param $filename
     */
    protected function setFileName($filename)
    {
        $this->fileName = $filename;
    }

    /**
     * @return string
     */
    protected function getOriginalFileName()
    {
        //$filename = pathinfo($this->fileName, PATHINFO_FILENAME);
        return $this->originalFileName;
    }

    /**
     * Replaces characters in strings that are illegal/unsafe for filename.
     *
     * #my*  unsaf<e>&file:name?".png
     *
     * @param string $filename the source filename to be "sanitized"
     * @return boolean string the sanitized filename
     */
    public static function sanitize($filename)
    {
        return str_replace([' ', '"', '\'', '&', '/', '\\', '?', '#'], '-', $filename);
    }

    /**
     * Generates random filename.
     * @param UploadedFile $file
     * @return string
     */
    protected function generateFileName($file)
    {
        return uniqid() . '.' . $file->extension;
    }

    /**
     * This method is invoked after uploading a file.
     * The default implementation raises the [[EVENT_AFTER_UPLOAD]] event.
     * You may override this method to do postprocessing after the file is uploaded.
     * Make sure you call the parent implementation so that the event is raised properly.
     */
    protected function afterUpload()
    {
        $this->owner->trigger(self::EVENT_AFTER_UPLOAD);
    }


    /**
     * This method is invoked before validation starts.
     */
    public function test()
    {
        return true;
    }


}

<?php

namespace lo\modules\gallery\components;

use lo\modules\gallery\behaviors\GalleryImageBehavior;
use lo\modules\gallery\behaviors\UploadedRemoteFile;
use lo\modules\gallery\helpers\FileHelper;
use yii\base\BaseObject;
use yii\web\UploadedFile;

class FnGenerator
{
    /**
     * @var GalleryImageBehavior
     */
    private $gallery;

    /**
     * @var BaseObject
     */
    private $file;

    /**
     * NameGenerator constructor.
     *
     * @param GalleryImageBehavior $gallery
     * @param BaseObject|[]        $file
     */
    public function __construct(GalleryImageBehavior $gallery, $file)
    {
        $this->gallery = $gallery;
        $this->file = $file;
    }

    public function generate(): string
    {
        $gallery = $this->gallery;
        $gallery->originalNameDelimiter = '~';
        $path = $gallery->getPath();

        $file = $this->file;

        /** @var UploadedFile $file */
        if ($file instanceof UploadedFile) {
            $filename = \explode($gallery->originalNameDelimiter, $file->baseName);
            $slug = $filename[0] . '.' . $file->extension;
        } elseif ($file instanceof UploadedRemoteFile) {
            $slug = $gallery->getImageName() . '.' . $file->extension;
        } else {
            /** @var array $file pathinfo() */
            $slug = $file['basename'] ? $file['filename'] : 'img.' . $file['extension'];
        }

        return FileHelper::getNameForSave($path, $slug);
    }
}

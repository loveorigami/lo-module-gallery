<?php
namespace lo\modules\gallery\models;

use lo\modules\gallery\behaviors\GalleryImageBehavior;
use lo\modules\gallery\helpers\FileHelper;
use Yii;
use lo\core\db\MetaFields;
use lo\core\db\fields;
use yii\web\UploadedFile;

/**
 * Class GalleryCatMeta
 * @package lo\modules\gallery\models
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class GalleryCatMeta extends MetaFields
{
    const GALLERY_TAB = "gallery";
    const GIN_GALLERY = '_gingallery';

    /**
     * @inheritdoc
     */
    public function tabs()
    {
        $tabs = parent::tabs();
        $tabs[self::GALLERY_TAB] = Yii::t('backend', "Gallery");
        return $tabs;
    }

    /**
     * @return array
     */
    protected function config()
    {
        /** @var GalleryCat $owner */
        $owner = $this->owner;
        $gallery = self::GIN_GALLERY;

        return [
            "name" => [
                "definition" => [
                    "class" => fields\TextField::class,
                    "title" => Yii::t('backend', 'Name'),
                    "showInGrid" => true,
                    "showInFilter" => true,
                    "isRequired" => false,
                    "editInGrid" => true,
                ],
                "params" => [$this->owner, "name"]
            ],
            "slug" => [
                "definition" => [
                    "class" => fields\SlugField::class,
                    "title" => Yii::t('backend', 'Slug'),
                    "showInGrid" => true,
                    "showInExtendedFilter" => false,
                    "generateFrom" => "name",
                ],
                "params" => [$this->owner, "slug"]
            ],
            "gal" => [
                "definition" => [
                    "class" => fields\ImageGalleryField::class,
                    "title" => Yii::t('backend', 'Gallery'),
                    "tab" => self::GALLERY_TAB,
                    "galleryBehavior" => $owner::GALLERY_ONE,
                    'uploadOptions' => [
                        "entity" => $owner::getEntityName(),
                        'removeDirectoryOnDelete' => true,
                        'extensions' => 'jpeg, jpg, png, gif',
                        'maxSize' => 1024 * 1024 * 10,
                        'path' => "@storagePath/$gallery/cat/{slug}",
                        'url' => "@storageUrl/$gallery/cat/{slug}",
                        'thumbPath' => '@storagePath/galleries/{slug}',
                        'thumbUrl' => '@storageUrl/galleries/{slug}',
                        'thumbs' => [
                            $owner::THUMB_ORI => [
                                'width' => 6000, 'height' => 6000, 'quality' => 90, 'mode' => 'bestFit',
                            ],
                            $owner::THUMB_BIG => [
                                'width' => 1200, 'height' => 900, 'quality' => 90, 'mode' => 'bestFit'
                            ],
                            $owner::THUMB_TMB => [
                                'width' => 500, 'height' => 500, 'quality' => 90, 'mode' => 'bestFit'
                            ],
                        ],
                        'generateNewName' => function ($file) use ($owner) {
                            /** @var GalleryImageBehavior $gallery */
                            $gallery = $owner->getBehavior($owner::GALLERY_ONE);
                            $gallery->originalNameDelimiter = '~';
                            $path = $gallery->getPath();

                            /** @var UploadedFile $file */
                            if ($file instanceof UploadedFile) {
                                $filename = explode($gallery->originalNameDelimiter, $file->baseName);
                                $name = $filename[0] . '.' . $file->extension;
                            } else {
                                /** @var array $file pathinfo() */
                                $name = $file['basename']
                                    ? $file['filename']
                                    : $owner->slug . '.' . $file['extension'];
                            }

                            return FileHelper::getNameForSave($path, $name);
                        },
                    ],
                    "showInGrid" => false,
                ],
                "params" => [$this->owner, "gal"]
            ],
        ];
    }
}
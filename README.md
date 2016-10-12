# Getting started with Lo-module-gallery

## Update database schema

The last thing you need to do is updating your database schema by applying the
migrations. Make sure that you have properly configured `db` application component
and run the following command:

```bash
$ php yii migrate/up --migrationPath=@vendor/loveorigami/lo-module-gallery/migrations
$ php yii migrate/down --migrationPath=@vendor/loveorigami/lo-module-gallery/migrations
```

## Create database schema
```bash
$ php yii migrate/create --migrationPath=@vendor/loveorigami/lo-module-gallery/migrations "gallery_"
```

## Add to Model
```php
    const THUMB_TMB = 'tmb';
    const THUMB_BIG = 'big';

    const GALLERY_ONE = 'gallery';

    public $gal;
```


## Add to MetaModel
```php
            "gal" => [
                "definition" => [
                    "class" => fields\ImageGalleryField::class,
                    "title" => Yii::t('backend', 'Gallery'),
                    "tab" => self::GALLERY_TAB,
                    "galleryBehavior" => $owner::GALLERY_ONE,
                    'uploadOptions' => [
                        "entity" => 'objItem',
                        'removeDirectoryOnDelete' => true,
                        'extensions' => 'jpeg, jpg, png, gif',
                        'maxSize' => 1024 * 1024 * 2,
                        'path' => '@storagePath/gallery/objects/{slug}',
                        'url' => '@storageUrl/gallery/objects/{slug}',
                        'thumbPath' => '@storagePath/objects/gallery/{slug}',
                        'thumbUrl' => '@storageUrl/objects/gallery/{slug}',
                        'thumbs' => [
                            GalleryItem::THUMB_TMB => [
                                'width' => 280, 'height' => 210, 'quality' => 90
                            ],
                            GalleryItem::THUMB_BIG => [
                                'width' => 1024, 'height' => 768, 'quality' => 90, 'mode' => 'best_fit',
                                'watermark' => function ($width, $height) {
                                    if ($width > 480 || $height > 480) {
                                        $path = '@storagePath/gallery/watermarks/wm200.png';
                                    } else {
                                        $path = '@storagePath/gallery/watermarks/wm100.png';
                                    }
                                    return $path;
                                },
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
                                $name = $owner->slug . '_' . $filename[0] . '.' . $file->extension;
                            } else {
                                /** @var array $file pathinfo() */
                                $name = $file['basename']
                                    ? $owner->slug . '_' . $file['filename']
                                    : $owner->slug . '.' . $file['extension'];
                            }

                            return FileHelper::getNameForSave($path, $name);
                        },
                    ]
                ],
                "params" => [$this->owner, "gal"]
            ],
```


## Widget in view
```php
            echo GalleryShow::widget([
                'gallery' => $model->getBehavior($model::GALLERY_ONE),
                'thumb' => $model::THUMB_TMB,
                'big' => $model::THUMB_BIG,
                'onmain' => true,
                'cols' => 2
            ]);
```
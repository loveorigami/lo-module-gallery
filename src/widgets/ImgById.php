<?php

namespace lo\modules\gallery\widgets;

use lo\modules\gallery\models\GalleryCat;
use lo\modules\gallery\models\GalleryItem;
use lo\plugins\shortcodes\ShortcodeWidget;
use Yii;
use yii\caching\TagDependency;

/**
 * Class GalleryWidget
 * @package lo\modules\gallery\plugins\gallery
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class ImgById extends ShortcodeWidget
{
    /**
     * Default width
     * @var int
     */
    const WIDTH = 250;

    /**
     * @var int
     */
    const IMG_CLASS = 'img-zoom';

    /**
     * Default width
     * @var int
     */
    const PULL_LEFT = 'left';
    const PULL_RIGHT = 'right';
    const PULL_CENTER = 'center';

    /**
     * @var string
     */
    public $width;

    /**
     * @var string
     */
    public $pull;

    /**
     * @var integer
     */
    public $id;

    /**
     * Init widget
     */
    public function init()
    {
        parent::init();

        if (!$this->width) {
            $this->view = self::WIDTH;
        }

        if (!$this->pull) {
            $this->pull = self::PULL_LEFT;
        }

    }

    /**
     * Render widget
     * @return string
     */
    public function run()
    {
        if ($this->id) {
            $result = Yii::$app->cacheCommon->getOrSet([
                GalleryItem::IMG_KEY,
                'id' => $this->id,
                'pull' => $this->getPull(),
                'width' => $this->width
            ], function () {
                /** @var GalleryItem $model */
                $model = GalleryItem::find()
                    ->alias('i')
                    ->innerJoinWith('cat c')
                    ->where(['i.id' => $this->id])
                    ->andWhere([
                        'i.entity' => GalleryCat::class
                    ])
                    ->published()
                    ->one();

                if ($model) {
                    /** @var GalleryCat $category */
                    $category = $model->cat;
                    /** @var \lo\modules\gallery\behaviors\GalleryImageBehavior $gallery */
                    $gallery = $category->getBehavior($category::GALLERY_ONE);

                    return $this->render('img', [
                        'width' => $this->width,
                        'pull' => $this->getPull(),
                        'img' => $this->getThumb($gallery, $model),
                        'model' => $model,
                        'gallery' => $gallery,
                    ]);
                }

                return null;

            }, null, new TagDependency(['tags' => [GalleryItem::IMG_KEY]]));

            return $result;

        }

        return null;
    }

    /**
     * @return string
     */
    protected function getPull()
    {
        $prefix = $this->pull == self::PULL_CENTER ? 'text' : 'pull';
        return $prefix . '-' . $this->pull . ' gallery-img';
    }

    /**
     * @param \lo\modules\gallery\behaviors\GalleryImageBehavior $gallery
     * @param $model
     * @return string
     */
    protected function getThumb($gallery, $model)
    {
        $tmb = $this->pull == self::PULL_CENTER ? GalleryCat::THUMB_BIG : GalleryCat::THUMB_TMB;
        $img = $gallery->getThumbUploadUrl($model->image, $tmb);
        return $img;
    }
}
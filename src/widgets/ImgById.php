<?php

namespace lo\modules\gallery\widgets;

use lo\modules\gallery\models\GalleryCat;
use lo\modules\gallery\models\GalleryItem;
use lo\plugins\shortcodes\ShortcodeWidget;

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
     * Default width
     * @var int
     */
    const PULL = 'left';

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
            $this->pull = self::PULL;
        }

    }

    /**
     * Render widget
     * @return string
     */
    public function run()
    {
        if ($this->id) {
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
                $gallery = $category->getBehavior($category::GALLERY_ONE);

                return $this->render('img', [
                    'width' => $this->width,
                    'pull' => $this->pull,
                    'model' => $model,
                    'gallery' => $gallery,
                ]);

            }
            return null;
        }
        return null;
    }
}
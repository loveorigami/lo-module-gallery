<?php
namespace lo\modules\gallery\widgets;

use lo\modules\gallery\models\GalleryCat;
use lo\modules\gallery\repository\ImageRepository;
use lo\plugins\shortcodes\ShortcodeWidget;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

/**
 * Class GalleryWidget
 * @package lo\modules\gallery\plugins\gallery
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class GalleryById extends ShortcodeWidget
{
    /**
     * Default view
     * @var string
     */
    const VIEW = 'gallery-show';

    /**
     * Default columns
     * @var int
     */
    const COLS = 6;

    /**
     * Default columns
     * @var int
     */
    const LIMIT = 60;

    /**
     * Rendered view
     * @var string
     */
    public $view;

    /**
     * Columns in row
     * @var int
     */
    public $cols;

    /**
     * Limit images on page
     * @var int
     */
    public $limit;

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

        if (!$this->view) {
            $this->view = self::VIEW;
        }

        if (!$this->cols) {
            $this->cols = self::COLS;
        }

        if (!$this->limit) {
            $this->limit = self::LIMIT;
        }
    }

    /**
     * Render widget
     * @return string
     */
    public function run()
    {
        if ($this->id) {
            /** @var GalleryCat $model */
            $model = GalleryCat::find()->where(['id' => $this->id])->limit(1)->published()->one();

            if ($model) {
                /** @var ImageRepository $gallery */
                $gallery = $model->getBehavior($model::GALLERY_ONE);

                $query = $gallery->findImages()->published();
                $query->limit($this->limit);

                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => false
                ]);

                $viewParams = [
                    'gallery' => $gallery,
                    'thumb' => $model::THUMB_TMB,
                    'big' => $model::THUMB_BIG,
                    'cols' => $this->cols
                ];

                return $this->render($this->view, [
                    'dataProvider' => $dataProvider,
                    'viewParams' => $viewParams,
                    'id' => 'w' . $this->id
                ]);

            }
            return null;
        }
        return null;
    }
}
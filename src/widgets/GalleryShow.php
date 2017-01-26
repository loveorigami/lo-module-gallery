<?php

namespace lo\modules\gallery\widgets;

use lo\modules\gallery\repository\ImageRepository;
use yii\base\Widget;
use yii\data\ActiveDataProvider;


/**
 * Widget to manage gallery.
 * Requires Twitter Bootstrap styles to work.
 * @property ImageRepository $gallery
 */
class GalleryShow extends Widget
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
     * Default images
     */
    const THUMB_TMB = 'tmb';
    const THUMB_BIG = 'big';

    /**
     * @var object ImageRepository
     */
    public $gallery;

    /**
     * Thumb name
     * @var string
     */
    public $thumb;
    public $big;

    /**
     * Rendered view
     * @var string
     */
    public $view;

    /**
     * @var bool
     */
    public $onmain = false;

    /**
     * Columns in row
     * @var int
     */
    public $cols;

    /**
     * Limit images on page
     * @var int
     */
    public $pageSize = 60;

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

        if (!$this->thumb) {
            $this->thumb = self::THUMB_TMB;
        }

        if (!$this->big) {
            $this->big = self::THUMB_BIG;
        }
    }

    /**
     * Render widget
     * @return string
     */
    public function run()
    {
        $pagination = [
            'pageSize' => $this->pageSize,
            'defaultPageSize' => $this->pageSize
        ];

        $query = $this->gallery->findImages()->published();

        if ($this->onmain) {
            $query->onmain()->limit($this->pageSize);
            $pagination = false;

        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        $viewParams = [
            'gallery' => $this->gallery,
            'thumb' => $this->thumb,
            'big' => $this->big,
            'cols' => $this->cols
        ];

        return $this->render($this->view, [
            'dataProvider' => $dataProvider,
            'viewParams' => $viewParams,
            'id' => $this->id
        ]);
    }
}

<?php

namespace lo\modules\gallery\widgets;

use lo\modules\gallery\repository\ImageRepository;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;


/**
 * Widget to manage gallery.
 * Requires Twitter Bootstrap styles to work.
 *
 * @property ImageRepository $gallery
 */
class GalleryShow extends Widget
{
    /**
     * Default view
     *
     * @var string
     */
    protected const VIEW = 'gallery-show';

    /**
     * Default columns
     *
     * @var int
     */
    protected const COLS = 6;

    /**
     * Default images
     */
    protected const THUMB_TMB = 'tmb';
    protected const THUMB_BIG = 'big';

    /**
     * @var object ImageRepository
     */
    public $gallery;

    /**
     * Thumb name
     *
     * @var string
     */
    public $thumb;
    public $big;

    /**
     * Rendered view
     *
     * @var string
     */
    public $view;

    /**
     * @var bool
     */
    public $onmain = false;

    /**
     * Columns in row
     *
     * @var int
     */
    public $cols;

    /**
     * Limit images on page
     *
     * @var int
     */
    public $pageSize = 60;
    public $urlHash;

    /**
     * @var array
     */
    public $thumbOptions = [];

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
     *
     * @return string
     */
    public function run(): string
    {
        $pagination = [
            'pageSize' => $this->pageSize,
            'pageParam' => 'page',
            'defaultPageSize' => $this->pageSize,
        ];

        if ($this->urlHash) {
            $pagination['params'] = ArrayHelper::merge($_GET, ['#' => $this->urlHash]);
        }

        $query = $this->gallery->findImages()->published();

        if ($this->onmain) {
            $query->onmain()->limit($this->pageSize);
            $pagination = false;

        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        $viewParams = [
            'gallery' => $this->gallery,
            'thumb' => $this->thumb,
            'thumbOptions' => $this->thumbOptions,
            'big' => $this->big,
            'cols' => $this->cols,
        ];

        return $this->render($this->view, [
            'dataProvider' => $dataProvider,
            'viewParams' => $viewParams,
            'id' => $this->id,
        ]);
    }
}

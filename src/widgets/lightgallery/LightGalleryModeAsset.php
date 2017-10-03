<?php

namespace lo\modules\gallery\widgets\lightgallery;

use yii\web\AssetBundle;

/**
 * Class LightGalleryModeAsset
 * @package lo\modules\gallery\widgets\lightgallery
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class LightGalleryModeAsset extends AssetBundle
{
    /**
     * @inherit
     */
    public $css = [
        'css/lg.css',
    ];

    /**
     * Initializes the bundle.
     * Set publish options to copy only necessary files (in this case css and font folders)
     * @codeCoverageIgnore
     */
    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . "/assets";
    }
}
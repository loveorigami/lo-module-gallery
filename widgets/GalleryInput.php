<?php

namespace lo\modules\gallery\widgets;

use lo\modules\gallery\behaviors\GalleryImageBehavior;
use lo\modules\gallery\models\GalleryItem;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\InputWidget;

/**
 * Widget to manage gallery.
 * Requires Twitter Bootstrap styles to work.
 */
class GalleryInput extends InputWidget
{
    /** @var ActiveRecord */
    public $model;
    public $attribute;

    /** @var string */
    public $galleryBehavior;

    /** @var GalleryImageBehavior Model of gallery to manage */
    private $behavior;

    /** @var string Route to gallery controller */
    public $apiRoute = false;

    public $options = [];

    public function init()
    {
        parent::init();
        $this->behavior = $this->model->getBehavior($this->galleryBehavior);
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        $app = Yii::$app;
        // add module I18N category
        if (!isset($app->i18n->translations['lo/gallery'])) {
            $app->i18n->translations['lo/gallery'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => '@lo/modules/gallery/messages',
                'fileMap' => [
                    'lo/gallery' => 'core.php',
                ],
            ];
        }
    }


    /** Render widget */
    public function run()
    {
        if ($this->apiRoute === null) {
            throw new Exception('$apiRoute must be set.', 500);
        }

        $images = [];
        foreach ($this->behavior->getImages()->all() as $image) {
            /** @var GalleryItem $image */
            $images[] = [
                'id' => $image->id,
                'pos' => $image->pos,
                'name' => (string)$image->name,
                'description' => (string)$image->description,
                'preview' => $this->behavior->getThumbUploadUrl($image->image, $image::THUMB_BIG),
            ];
        }

        $baseUrl = [
            $this->apiRoute,
            //'type' => $this->behavior->type,
            'galleryBehavior' => $this->galleryBehavior,
            'galleryId' => $this->model->getPrimaryKey(),
        ];

        $opts = array(
            //'hasName' => $this->behavior->hasName ? true : false,
            //'hasDesc' => $this->behavior->hasDescription ? true : false,
            'uploadUrl' => Url::to($baseUrl + ['action' => 'ajaxUpload']),
            'deleteUrl' => Url::to($baseUrl + ['action' => 'delete']),
            'updateUrl' => Url::to($baseUrl + ['action' => 'changeData']),
            'arrangeUrl' => Url::to($baseUrl + ['action' => 'order']),
            'nameLabel' => Yii::t('lo/gallery', 'Name'),
            'descriptionLabel' => Yii::t('lo/gallery', 'Description'),
            'photos' => $images,
        );

        $opts = Json::encode($opts);
        $view = $this->getView();
        GalleryInputAsset::register($view);
        $view->registerJs("$('#{$this->id}').galleryManager({$opts});");

        $this->options['id'] = $this->id;
        $this->options['class'] = 'gallery-manager';

        $fileOptions = [
            'class' => "afile",
            'accept' => "image/*",
            'multiple' => "multiple"
        ];

        if ($this->hasModel()) {
            $html = Html::activeFileInput($this->model, $this->attribute, $fileOptions);
        } else {
            $html = Html::fileInput($this->name, $this->value, $fileOptions);
        }

        return $this->render('manager', ['html' => $html]);
    }

}

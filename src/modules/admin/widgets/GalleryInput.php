<?php

namespace lo\modules\gallery\modules\admin\widgets;

use devgroup\dropzone\DropZone;
use lo\modules\gallery\behaviors\GalleryImageBehavior;
use yii\db\ActiveRecord;
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

    public $pjaxContainer = '#gallery-content';

    public $options = [];

    public function init()
    {
        parent::init();
        $this->behavior = $this->model->getBehavior($this->galleryBehavior);
    }


    /** Render widget */
    public function run()
    {
        $images = $this->behavior->findImages()->all();

        $this->options['id'] = $this->id;
        $this->options['class'] = 'gallery-manager';

        $baseUrl = [
            $this->apiRoute,
            //'type' => $this->behavior->type,
            'galleryBehavior' => $this->galleryBehavior,
            'galleryId' => $this->model->getPrimaryKey(),
        ];

        $optsArr = [
            'pjaxContainer' => $this->pjaxContainer,
            'pjaxUrl' => Url::current(),
            'deleteUrl' => Url::to($baseUrl + ['action' => 'delete']),
            'updateUrl' => Url::to($baseUrl + ['action' => 'update']),
            'orderUrl' => Url::to($baseUrl + ['action' => 'order']),
            'statusUrl' => Url::to($baseUrl + ['action' => 'status'])
        ];

        $opts = Json::encode($optsArr);

        $view = $this->getView();
        GalleryInputAsset::register($view);
        $view->registerJs('Dropzone.autoDiscover = false;', $view::POS_END);
        $view->registerJs("
            $('#{$this->id}').galleryManager({$opts});
            $(document).ajaxComplete(function(){
                $('#{$this->id}').galleryManager({$opts});
            });
        ");
        $pjaxUrl = $optsArr['pjaxUrl'];
        $pjaxContainer = $optsArr['pjaxContainer'];

        $config = [
            'url' => Url::to($baseUrl + ['action' => 'upload']), // upload url
            'storedFiles' => [], // stores files
            'eventHandlers' => [
                'sending' => "function(file, xhr, formData) {
                    formData.append('toStart', $('#to-start').prop('checked'));
                }",
                'queuecomplete' => "function(file) {
                     $.pjax.reload({
                        container: '$pjaxContainer',
                        url: '$pjaxUrl',
                        showNoty: false
                    })
                }",
            ],
            'sortable' => true, // sortable flag
            'sortableOptions' => [], // sortable options
            'htmlOptions' => [], // container html options
            'options' => [], // dropzone js options
        ];

        if ($this->hasModel()) {
            $config['model'] = $this->model;
            $config['attribute'] = $this->attribute;
        } else {
            $config['name'] = 'file';
        }

        $html = DropZone::widget($config);

        return $this->render('manager', [
            'gallery' => $this->behavior,
            'images' => $images,
            'html' => $html
        ]);
    }

}

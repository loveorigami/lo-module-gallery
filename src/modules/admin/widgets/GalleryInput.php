<?php

namespace lo\modules\gallery\modules\admin\widgets;

use devgroup\dropzone\DropZone;
use lo\modules\gallery\behaviors\GalleryImageBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
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

    public $maxFiles;

    public function init()
    {
        parent::init();
        $this->behavior = $this->model->getBehavior($this->galleryBehavior);
    }

    /** Render widget */
    public function run(): string
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
            'statusUrl' => Url::to($baseUrl + ['action' => 'status']),
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
            'message' => Yii::t('gallery', 'Drop Files Here…'),
            'options' => [], // dropzone js options
        ];

        if ($this->hasModel()) {
            $config['model'] = $this->model;
            $config['attribute'] = $this->attribute;
        } else {
            $config['name'] = 'file';
        }

        $maxFilesMsg = '';
        if ($this->maxFiles) {
            $msg = Yii::t('gallery', 'Max upload files is {count}', ['count' => $this->maxFiles]);
            $config['storedFiles'] = $this->getStoredFiles($images);
            $config['options']['init'] = new JsExpression('
                    function() {
                        this.on("maxfilesexceeded", function(file){
                            alert("' . $msg . '");
                        });
                    }'
            );
            $config['options']['maxFiles'] = $this->maxFiles;
            if (\count($images) >= $this->maxFiles) {
                $maxFilesMsg = Html::tag('p', $msg, ['class' => 'alert alert-info']);
            }
        }

        $html = DropZone::widget($config);

        return $this->render('manager', [
            'gallery' => $this->behavior,
            'images' => $images,
            'html' => $html,
            'maxFilesMsg' => $maxFilesMsg,
        ]);
    }

    /**
     * @param $images
     * @return array
     * @throws \yii\base\Exception
     */
    protected function getStoredFiles($images): array
    {
        $data = [];
        $gallery = $this->behavior;
        if ($this->maxFiles) {
            foreach ($images as $model) {
                $path = $gallery->getThumbUploadPath($model->image, $model::THUMB_TMB);
                $filesize = $path ? filesize($path) : 0;
                $data[] = [
                    'name' => $model->name,
                    'thumbnail' => $gallery->getThumbUploadUrl($model->image, $model::THUMB_TMB),
                    'size' => $filesize,
                ];
            }
        }

        return $data;
    }
}

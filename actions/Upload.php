<?php

namespace lo\modules\gallery\actions;

use lo\core\actions\Base;
use lo\core\db\ActiveRecord;
use lo\modules\gallery\behaviors\GalleryImageBehavior;
use lo\modules\gallery\models\GalleryItem;
use Yii;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;


/**
 * Backend controller for GalleryManager widget.
 * Provides following features:
 *  - Image removal
 *  - Image upload/Multiple upload
 *  - Arrange images in gallery
 *  - Changing name/description associated with image
 */
class Upload extends Base
{
    /** @var string сценарий валидации */
    public $modelScenario = ActiveRecord::SCENARIO_UPDATE;

    /** @var string поведение */
    public $galleryBehavior;

    /** @var GalleryImageBehavior */
    private $behavior;

    /** @var  ActiveRecord */
    private $owner;

    /**
     * Запуск действия
     * @param $action
     * @return boolean
     * @throws ForbiddenHttpException | NotFoundHttpException
     */
    public function run($action)
    {
        $request = Yii::$app->request;


        if ($request->isPost) {
            $pk = Yii::$app->request->get('galleryId');
            $this->galleryBehavior = Yii::$app->request->get('galleryBehavior');

            $this->owner = $this->findModel($pk);

            if ($this->owner == null) {
                throw new NotFoundHttpException('Gallery not found.');
            }

            $this->owner->setScenario($this->modelScenario);
            $this->behavior = $this->owner->getBehavior($this->galleryBehavior);


            //$this->owner->save();

            switch ($action) {
                case 'delete':
                    //return $this->actionDelete(Yii::$app->request->post('id'));
                    return 1;
                    break;

                case
                'ajaxUpload':
                    return $this->actionAjaxUpload();
                    break;

                /*                case 'changeData':
                                    return $this->actionChangeData(Yii::$app->request->post('photo'));

                                case 'order':
                                    return $this->actionOrder(Yii::$app->request->post('order'));

                                default:
                                    throw new BadRequestHttpException('Action do not exists');*/
            }

            /*            if (!Yii::$app->user->can($this->access(), array("model" => $model)))
                            throw new ForbiddenHttpException('Forbidden');*/

            //$model->setScenario($this->modelScenario);

            //return $model->save();
        }

        return false;
    }

    /**
     * Method to handle file upload thought XHR2
     * On success returns JSON object with image info.
     * @return string
     * @throws HttpException
     */
    public function actionAjaxUpload()
    {
        $this->behavior->uploadFile();
        /** @var GalleryItem $image */
        $image = $this->behavior->loadModel();

        // not "application/json", because  IE8 trying to save response as a file
        Yii::$app->response->headers->set('Content-Type', 'text/html');

        return Json::encode(
            array(
                'id' => $image->id,
                'pos' => $image->pos,
                'name' => (string)$image->name,
                'description' => (string)$image->description,
                'preview' => $this->behavior->getThumbUploadUrl($image->image, $image::THUMB_BIG),
            )
        );
    }
}

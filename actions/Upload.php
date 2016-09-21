<?php

namespace lo\modules\gallery\actions;

use lo\core\actions\Base;
use lo\core\db\ActiveRecord;
use lo\modules\gallery\behaviors\GalleryImageBehavior;
use lo\modules\gallery\models\GalleryItem;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
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
     * @param $action
     * @return bool|string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
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

            switch ($action) {
                case 'delete':
                    return $this->delete(Yii::$app->request->post('id'));
                    break;

                case 'upload':
                    return $this->ajaxUpload();
                    break;

                case 'status':
                    return $this->status(Yii::$app->request->post('status'));
                    break;

                case 'order':
                    return $this->reOrder(Yii::$app->request->post('order'));
                    break;

                case 'update':
                    return $this->changeData(Yii::$app->request->post('photo'));
                    break;

                default:
                    throw new BadRequestHttpException('Action do not exists');
            }

        }

        return false;
    }

    /**
     * Removes image with ids specified in post request.
     * On success returns 'OK'
     * @param $ids
     * @throws HttpException
     * @return string
     */
    private function delete($ids)
    {
        $this->behavior->deleteImages($ids);
        Yii::$app->session->setFlash('success', 'Delete success');
        return 'OK';
    }

    /**
     * On success returns 'OK'
     * @param $ids
     * @throws HttpException
     * @return string
     */
    private function status($ids)
    {
        $this->behavior->statusImages($ids);
        Yii::$app->session->setFlash('success', 'Status success');
        return 'OK';
    }

    /**
     * Method to handle file upload thought XHR2
     * On success returns JSON object with image info.
     * @return string
     * @throws HttpException
     */
    private function ajaxUpload()
    {
        $result = $this->behavior->uploadFile();

        $data['result'] = $result;

        if (!$result) {
            $data['errors'] = Html::errorSummary($this->owner, ['header' => false]);
        } else {
            /** @var GalleryItem $image */
            $image = $this->behavior->getModel();

            // not "application/json", because  IE8 trying to save response as a file
            Yii::$app->response->headers->set('Content-Type', 'text/html');

            $data['image'] = [
                'id' => $image->id,
                'pos' => $image->pos,
                'status' => $image->status,
                'name' => (string)$image->name,
                'description' => (string)$image->description,
                'preview' => $this->behavior->getThumbUploadUrl($image->image, $image::THUMB_TMB),
            ];
        }

        return Json::encode($data);
    }

    /**
     * Method to update images name/description via AJAX.
     * On success returns JSON array od objects with new image info.
     *
     * @param $imagesData
     *
     * @throws HttpException
     * @return string
     */
    public function changeData($imagesData)
    {
        if (count($imagesData) == 0) {
            throw new BadRequestHttpException('Nothing to save');
        }

        $images = $this->behavior->updateImages($imagesData);

        $resp = [];

        foreach ($images as $image) {
            $resp[] = [
                'id' => $image->id,
                'pos' => $image->pos,
                'name' => (string)$image->name,
                'description' => (string)$image->description,
                'preview' => $this->behavior->getThumbUploadUrl($image->image, $image::THUMB_BIG),
            ];
        }

        Yii::$app->session->setFlash('success', 'Update success');

        return Json::encode($resp);
    }

    /**
     * Saves images order according to request.
     * Variable $_POST['order'] - new arrange of image ids, to be saved
     * @param $order
     * @return string
     * @throws BadRequestHttpException
     */
    private function reOrder($order)
    {
        if (count($order) == 0) {
            throw new BadRequestHttpException('No data, to save');
        }

        $res = $this->behavior->reOrder($order);
        Yii::$app->session->setFlash('success', 'Reorder success');

        return Json::encode($res);
    }
}

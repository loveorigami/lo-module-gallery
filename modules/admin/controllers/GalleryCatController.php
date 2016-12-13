<?php

namespace lo\modules\gallery\modules\admin\controllers;

use lo\modules\gallery\models\GalleryCat;
use yii\web\Controller;
use lo\core\actions\crud;

/**
 * GalleryCatController implements the CRUD actions for Country model.
 */
class GalleryCatController extends Controller
{
    /**
     * Действия
     * @return array
     */

    public function actions()
    {
        $class = GalleryCat::class;
        return [
            'index'=>[
                'class'=> crud\Index::class,
                'modelClass'=>$class,
            ],
            'view'=>[
                'class'=> crud\View::class,
                'modelClass'=>$class,
            ],
            'create'=>[
                'class'=> crud\Create::class,
                'modelClass'=>$class,
            ],
            'update'=>[
                'class'=> crud\Update::class,
                'modelClass'=>$class,
            ],
            'delete'=>[
                'class'=> crud\Delete::class,
                'modelClass'=>$class,
            ],
            'groupdelete'=>[
                'class'=>crud\GroupDelete::class,
                'modelClass'=>$class,
            ],

            'editable'=>[
                'class'=>crud\XEditable::class,
                'modelClass'=>$class,
            ],

        ];
    }

}

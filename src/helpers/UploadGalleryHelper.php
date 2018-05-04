<?php

namespace lo\modules\gallery\helpers;

use lo\modules\gallery\behaviors\UploadedRemoteFile;

/**
 * Class UploadHelper
 * @package lo\core\helpers
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class UploadGalleryHelper
{
    /**
     * @param $url
     * @return UploadedRemoteFile
     */
    public static function fromUrl($url)
    {
        return UploadedRemoteFile::initWithUrl($url);
    }
}
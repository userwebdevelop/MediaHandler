<?php

namespace userwebdevelop\mediahandler\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MediaHandlerAsset extends AssetBundle
{
    public $sourcePath = '@vendor/userwebdevelop/media-handler/assets';
    public $css = [
        '/css/many-images-field.css',
    ];
    public $js = [
        '/js/many-images-field.js',
    ];
}

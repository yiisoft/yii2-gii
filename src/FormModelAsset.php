<?php

namespace yii\gii;


class FormModelAsset extends \yii\web\AssetBundle
{
    public $sourcePath = __DIR__;

    public $js = [
        'assets/js/add-remove.jquery.js',
        'assets/js/accordion.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

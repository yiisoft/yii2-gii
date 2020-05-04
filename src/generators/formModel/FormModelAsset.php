<?php

namespace yii\gii\generators\formModel;

//use yii\bootstrap4\BootstrapAsset;
use yii\web\JqueryAsset;

class FormModelAsset extends \yii\web\AssetBundle
{
    public $sourcePath = __DIR__;

    public $css = [
        'css/custom.css'
    ];

    public $js = [
        'js/add-remove.jquery.js',
        'js/accordion.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
//        BootstrapAsset::class,
    ];
}

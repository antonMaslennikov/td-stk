<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
		'packages/fancybox/jquery.fancybox.min.css',
        'packages/bootstrap-daterangepicker/daterangepicker.css',
    ];
    public $js = [
        'js/moment.min.js',
        'packages/fancybox/jquery.fancybox.min.js',
		'packages/bootstrap-daterangepicker/daterangepicker.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}

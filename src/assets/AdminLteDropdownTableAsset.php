<?php

namespace locky42\adminlte\dropdownTable\assets;

use yii\web\AssetBundle;

class AdminLteDropdownTableAsset extends AssetBundle
{
    public $sourcePath = '@vendor/locky42/yii2-adminlte-dropdown-table/src/web';

    public $js = [
        'js/dropDownTableAjax.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];

    public $publishOptions = [
        'forceCopy'=>true,
    ];
}

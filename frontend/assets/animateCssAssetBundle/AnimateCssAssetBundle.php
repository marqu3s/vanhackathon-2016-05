<?php
/**
 * Created by PhpStorm.
 * User: joao
 * Date: 15/09/15
 * Time: 00:59
 */

namespace frontend\assets\animateCssAssetBundle;

use yii\web\AssetBundle;

class AnimateCssAssetBundle extends AssetBundle
{
    public $sourcePath = '@frontend/assets/animateCssAssetBundle';
    public $depends = [
    ];
    public $css = [
        'css/animate.css',
    ];

    /**
     * Sets the [[publishOptions]] property.
     * Needed because it's necessary to concatenate the namespace value.
     */
    public function init()
    {
        parent::init();

        $this->publishOptions = [
            'forceCopy' => YII_DEBUG,
        ];
    }

}

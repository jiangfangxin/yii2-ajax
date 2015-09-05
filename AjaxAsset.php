<?php
/**
 * Author: Fangxin Jiang
 */

namespace smallbearsoft\ajax;

use yii\web\AssetBundle;

/**
 * This asset bundle provides the javascript files required by [[Ajax]] widget.
 *
 * @author Fangxin Jiang <2497085409@qq.com>
 */
class AjaxAsset extends AssetBundle
{
    public $sourcePath = '@vendor/smallbearsoft/yii2-ajax/js';
    public $js = [
        'ajaxHelper.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

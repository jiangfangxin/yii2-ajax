<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace smallbearsoft\ajax;

use yii\web\AssetBundle;

/**
 * This asset bundle provides the javascript files required by [[Pjax]] widget.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AjaxAsset extends AssetBundle
{
//    public $sourcePath = '@vendor/smallbearsoft/yii2-ajax/js';
    public $sourcePath = '@app/smallbearsoft/yii2-ajax/js';
    public $js = [
        'ajaxHelper.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

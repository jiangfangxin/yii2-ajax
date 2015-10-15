<?php
/**
 * Author: Fangxin Jiang
 * Date: 2015-8-27
 * Time: 21:18
 */

namespace smallbearsoft\ajax;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use smallbearsoft\ajax\AjaxAsset;

/**
 * Ajax is a widget extension of Yii2 which integrating the [jQuery Ajax](http://api.jquery.com/jQuery.ajax/).
 * It can respond the calls of tag (any html tag except from) click and form submission. By default, the tag or form you
 * want to use with Ajax should add a `data-ajax` attribute. The calls of tag and form enclosed between [[begin()]] and
 * [[end()]] will be regard as ajax request.
 *
 * You may configure [[linkSelector]] to specify which tags should trigger ajax, and configure [[formSelector]]
 * to specify which form submission may trigger ajax.
 *
 * The following example shows how to use Ajax to submit a form. To make it simple there will not use ActiveForm widget.
 *
 * SiteController.php
 * ```php
 * <?php
 *
 * namespace app\controllers;
 *
 * use Yii;
 * use yii\web\Controller;
 *
 * public function actionForm()
 * {
 *     return $this->render('form');
 * }
 *
 * public function actionPost()
 * {
 *     if(isset($_POST['name']) && isset($_POST['age'])) {
 *         $name = $_POST['name'];
 *         $age = $_POST['age'];
 *         return "Success, name is $name and age is $age.";
 *     } else {
 *         return 'Success, bat we can not get the name and age.';
 *     }
 * }
 * ```
 *
 * form.php
 * ```php
 * <?php
 *
 * use smallbearsoft\ajax\Ajax;
 * use yii\web\JsExpression;
 * use yii\helpers\Url;
 *
 * Ajax::begin(['clientOptions' => [
 *     'success' => new JsExpression('function(data, textStatus, jqXHR) {alert(data)}'),
 *     'error' => new JsExpression('function(jqXHR, textStatus, errorThrown) {alert(errorThrown)}'),
 *     'beforeSend' => new JsExpression('function(jqXHR, settings) {alert("Before send.")}'),
 *     'complete' => new JsExpression('function(jqXHR, textStatus) {alert("Complete.")}')
 * ]]) ?>
 *     <form action="<?= Url::to(['site/post'])?>" method="post" data-ajax="1">
 *         <input type="text" name="name" value="Fangxin Jiang"/>
 *         <input type="text" name="age" value="22"/>
 *         <input type="submit" value="Submit"/>
 *     </form>
 * <?php Ajax::end() ?>
 * ```
 *
 * @author Fangxin Jiang <2497085409@qq.com>
 */
class Ajax extends Widget
{
    /**
     * @var string The HTML container tag.
     */
    public $tag = 'div';
    /**
     * @var array The HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    /**
     * @var string The jQuery selector of the tags that should trigger ajax requests.
     * If not set, all tags (except form) with `data-ajax` attribute within the enclosed content of Ajax will trigger
     * ajax requests.
     */
    public $linkSelector;
    /**
     * @var string The jQuery selector of the forms whose submissions should trigger ajax requests.
     * If not set, all forms with `data-ajax` attribute within the enclosed content of Ajax will trigger ajax requests.
     */
    public $formSelector;
    /**
     * @var array Options to be passed to the jQuery Ajax. Please refer to the
     * [jQuery Ajax](http://api.jquery.com/jQuery.ajax/) for available options.
     */
    public $clientOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        echo Html::beginTag($this->tag, $this->options);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::endTag($this->tag);
        $this->registerClientScript();
    }

    /**
     * Generate a json object that jQuery Ajax needed.
     * In this method, we will add several extra javascript method to get the jQuery Ajax param's value form html tags'
     * attributes. We've defined some attributes for those frequently-used jQuery Ajax params. Such as: url, method,
     * data and so on. Usage: `<button data-url="post.php" ajax-data="name=Fangxin Jiang&age=22">Button</button>`
     * @return string Json Object.
     */
    public function generateClientOptions() {
        $helpers = [
            'url' => ['local'=>'ajaxHelper.getUrl(this)'],              //`ajax-url|href|action`
            'method' => [                                               //`ajax-method|method`
                'local'=>'ajaxHelper.getMethod(this)',
                'default'=>'ajaxHelper.getMethod_default(this)'
            ],
            'data' => ['local'=>'ajaxHelper.getData(this)'],            //`ajax-data`
            'dataType' => ['local'=>'ajaxHelper.getDataType(this)'],    //`ajax-dataType`
            'processData' => [                                          //`ajax-processData`
                'local'=>'ajaxHelper.getProcessData(this)',
                'default'=>'ajaxHelper.getProcessData_default(this)'
            ],
            'contentType' => [                                          //`ajax-contentType`
                'local'=>'ajaxHelper.getContentType(this)',
                'default'=>'ajaxHelper.getContentType_default(this)'
            ],
            'success' => ['local'=>'ajaxHelper.getSuccess(this)'],      //`ajax-success`
            'error' => ['local'=>'ajaxHelper.getError(this)'],          //`ajax-error`
            'beforeSend' => ['local'=>'ajaxHelper.getBeforeSend(this)'],//`ajax-beforeSend`
            'complete' => ['local'=>'ajaxHelper.getComplete(this)'],    //`ajax-complete`
            'cache' => ['local'=>'ajaxHelper.getCache(this)'],          //`ajax-cache`
            'timeout' => ['local'=>'ajaxHelper.getTimeout(this)']       //`ajax-timeout`
        ];
        foreach($helpers as $key => $method) {
            $local = isset($method['local']) ? $method['local'] . ',' : '';
            $default = isset($method['default']) ? ',' .$method['default'] : '';
            if(isset($this->clientOptions[$key]) && $this->clientOptions[$key] !== null) {
                if(is_string($this->clientOptions[$key])) {
                    $this->clientOptions[$key] = $local . '"' . $this->clientOptions[$key] . '"' . $default;
                } else if(is_bool($this->clientOptions[$key])) {
                    $this->clientOptions[$key] = $local . ($this->clientOptions[$key] ? 'true' : 'false') . $default;
                } else {    //Type of `$this->clientOptions[$key]` is `JsExpression` or other non string and non bool type.
                    if($this->clientOptions[$key] instanceof JsExpression)
                        $this->clientOptions[$key] = preg_replace('/;$/', '', $this->clientOptions[$key]->expression);
                    $this->clientOptions[$key] = $local . $this->clientOptions[$key] . $default;
                }
            } else {
                $this->clientOptions[$key] = $local . 'null' . $default;
            }
            $this->clientOptions[$key] = new JsExpression('ajaxHelper.priority(' . $this->clientOptions[$key] . ')');
        }
        return Json::encode($this->clientOptions);
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $id = $this->options['id'];
        $options = $this->generateClientOptions();
        $linkSelector = $this->linkSelector !== null ? $this->linkSelector : '#' . $id . ' *[data-ajax]:not(form)';
        $formSelector = $this->formSelector !== null ? $this->formSelector : '#' . $id . ' form[data-ajax]';
        $view = $this->getView();
        JqueryAsset::register($view);
        AjaxAsset::register($view);
        $js = "jQuery('$linkSelector').click(function() {jQuery.ajax(ajaxHelper.filter($options));return this.tagName.toLowerCase()=='a' ? false : true;});";
        $js .= "\njQuery(document).on('submit', '$formSelector', function() {jQuery.ajax(ajaxHelper.filter($options));return false;});";
        $view->registerJs($js);
    }
}
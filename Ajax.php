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
 * It can respond the calls of link click and form submission. By default, the link or form you want to use with ajax
 * should add a `data-ajax` attribute. The calls of link and form enclosed between [[begin()]] and [[end()]] will be
 * regard as ajax request.
 *
 * You may configure [[linkSelector]] to specify which links should trigger ajax, and configure [[formSelector]]
 * to specify which form submission may trigger ajax.
 *
 * The following example shows how to use Ajax to submit a form. To make it simple there will not use ActiveForm widget.
 *
 * siteController.php
 * ```php
 * public function actionPeople() {
 *     return $this->render('people');
 * }
 *
 * public function actionPost() {
 *     if(isset($_POST['name']) && isset($_POST['age'])) {
 *         return 'Name is ' . $_POST['name'] . '. Age is ' . $_POST['age'];
 *     } else {
 *         return 'Can't get the post data!';
 *     }
 * }
 * ```
 *
 * people.php
 * ```php
 * use smallbearsoft\ajax\Ajax;
 * use yii\helpers\Url;
 *
 * Ajax::begin([
 *     'success' => 'function(data, textStatus, jqXHR) {alert(data)}'
 * ]);
 *
 * <form action="<?= Url::to(['site/people'])?>" method="post" data-ajax="1">
 *     <input type="text" name="name" value="Fangxin Jiang"/>
 *     <input type="text" name="age" value="22"/>
 *     <input type="submit" value="Submit"/>
 * </form>
 *
 * Ajax::end();
 * ```
 *
 * @author Fangxin Jiang
 */
class Ajax extends Widget
{
    /**
     * @var array The HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    /**
     * @var string The jQuery selector of the links that should trigger ajax requests.
     * If not set, all links with `data-ajax` attribute within the enclosed content of Ajax will trigger ajax requests.
     */
    public $linkSelector;
    /**
     * @var string The jQuery selector of the forms whose submissions should trigger ajax requests.
     * If not set, all forms with `data-ajax` attribute within the enclosed content of Ajax will trigger ajax requests.
     */
    public $formSelector;
    /**
     * @var string This is to set the jQuery Ajax url param.
     * If set, all link and form with `data-ajax` within the enclosed content of Ajax will use this url as the jQuery Ajax url's value.
     * If not set, a javascript method `ajaxHelper.getUrl(this)` will be setted as the value of the jQuery Ajax url.
     * For link, the `ajaxHelper.getUrl(this)` method will return the `href` attribute's value as jQuery Ajax url's value.
     * For form, the `ajaxHelper.getUrl(this)` method will return the `action` attribute's value as jQuery Ajax url's value.
     * If both `href` and `action` are not set, the `ajaxHelper.getUrl(this)` method will return null.
     * Besides, you can use your custom method to replace `ajaxHelper.getUrl(this)` method. You can define a method refer
     * to `ajaxHelper.getUrl(this)` in ajaxHelper.js. For example:
     * ```php
     * use smallbearsoft\ajax\Ajax;
     * use yii\web\JsExpression;
     *
     * $this->registerJs("
     *     function myGetUrl(elem) {
     *         ~~~
     *     }
     * ");
     *
     * Ajax::begin([
     *     'url' => new JsExpression('myGetUrl(this)');
     * ]);
     * ~~~
     * Ajax::end();
     * ```
     */
    public $url;
    /**
     * @var string `GET` or `POST`. Refer to the jQuery Ajax method param.
     * If not set, a javascript method `ajaxHelper.getMethod(this)` will be setted as the value of the jQuery Ajax method.
     * For link, the `ajaxHelper.getMethod(this)` method will return the `ajax-method` attribute's value as jQuery Ajax method's value.
     * For form, the `ajaxHelper.getMethod(this)` method will return the `method` attribute's value as jQuery Ajax method's value.
     * If both `ajax-method` and `method` are not set, the `ajaxHelper.getMethod(this)` method will return "GET" for link, "POST" for from.
     * Besides, you can use your custom method to replace `ajaxHelper.getMethod(this)` method. You can define a method refer
     * to `ajaxHelper.getMethod(this)` in ajaxHelper.js.
     */
    public $method;
    /**
     * @var string|array|object This can be a string, json object or FormData object. Refer to the jQuery Ajax data param.
     * Recommend you to use string like `name=Fangxin Jiang&age=22` as data's value, if you want to transfer some data.
     * If not set, a javascript method `ajaxHelper.getData(this)` will be setted as the value of the jQuery Ajax data.
     * For link, the `ajaxHelper.getData(this)` method will return the `ajax-data` attribute's value as jQuery Ajax data's value.
     * For form, the `ajaxHelper.getData(this)` method will return a FormData object as jQuery Ajax data's value.
     * If `ajax-data` is not set, the `ajaxHelper.getData(this)` method will return null.
     * Besides, you can use your custom method to replace `ajaxHelper.getData(this)` method. You can define a method refer
     * to `ajaxHelper.getData(this)` in ajaxHelper.js.
     */
    public $data;
    /**
     * @var string The type of data that you're expecting back from the server. Value can be `text`, `xml`, `json`,
     * `script`, `html` or `jsonp`. Refer to the jQuery Ajax dataType param.
     * If not set, a javascript method `ajaxHelper.getDataType(this)` will be setted as the value of the jQuery Ajax dataType.
     * For both link and form, the `ajaxHelper.getDataType(this)` method will return the `ajax-dataType` attribute's value
     * as jQuery Ajax dataType's value.
     * If `ajax-dataType` is not set, the `ajaxHelper.getDataType(this)` method will return null. And so, jQuery will try
     * to infer the back data based on the MIME type of the response.
     * Besides, you can use your custom method to replace `ajaxHelper.getDataType(this)` method. You can define a method refer
     * to `ajaxHelper.getDataType(this)` in ajaxHelper.js.
     */
    public $dataType;
    /**
     * @var boolean Whether process and transform object data to query string. Refer to the jQuery Ajax processData param.
     * If not set, a javascript method `ajaxHelper.getProcessData(this)` will be setted as the value of the jQuery Ajax processData.
     * For form, the `ajaxHelper.getProcessData(this)` method will return false as jQuery Ajax processData's value. That's
     * because we use FormData object to collect form's inputs by default, and set the FormData object as jQuery Ajax data's value.
     * We needn't process and transfer this data. So we should set false as jQuery Ajax processData's value.
     * Besides, you can use your custom method to replace `ajaxHelper.getProcessData(this)` method. You can define a method
     * refer to `ajaxHelper.getProcessData(this)` in ajaxHelper.js.
     */
    public $processData;
    /**
     * @var string|boolean When sending data to the server, use this content type. Refer to the jQuery Ajax contentType param.
     * If not set, a javascript method `ajaxHelper.getContentType(this)` will be setted as the value of the jQuery Ajax contentType.
     * For form, the `ajaxHelper.getContentType(this)` method will return false as jQuery Ajax contentType's value.
     * Besides, you can use your custom method to replace `ajaxHelper.getContentType(this)` method. You can define a method
     * refer to `ajaxHelper.getContentType(this)` in ajaxHelper.js.
     */
    public $contentType;
    /**
     * @var string This string is a javascript function. Refer to the jQuery Ajax success param.
     */
    public $success;
    /**
     * @var string This string is a javascript function. Refer to the jQuery Ajax error param.
     */
    public $error;
    /**
     * @var string This string is a javascript function. Refer to the jQuery Ajax beforeSend param.
     */
    public $beforeSend;
    /**
     * @var string This string is a javascript function. Refer to the jQuery Ajax complete param.
     */
    public $complete;
    /**
     * @var Boolean Whether browser buffer requested pages. Refer to the jQuery Ajax cache param.
     */
    public $cache;
    /**
     * @var Number Set a timeout (in milliseconds) for the request. Refer to the jQuery Ajax timeout param.
     */
    public $timeout;
    /**
     * @var array Additional options to be passed to the jQuery Ajax. Please refer to the
     * [jQuery Ajax](http://api.jquery.com/jQuery.ajax/) for available options.
     */
    public $clientOptions;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        echo Html::beginTag('div', $this->options);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::endTag('div');
        $this->registerClientScript();
    }

    /**
     * Generate a json object that jQuery Ajax needed.
     * @return string Json Object.
     */
    public function generateClientOptions() {
        //Following client options can be set both in Ajax::begin() method and html element.
        $this->clientOptions['url'] = $this->url !== null ? $this->url : new JsExpression('ajaxHelper.getUrl(this)');
        $this->clientOptions['method'] = $this->method !== null ? $this->method : new JsExpression('ajaxHelper.getMethod(this)');
        $this->clientOptions['data'] = $this->data !== null ? $this->data : new JsExpression('ajaxHelper.getData(this)');
        $this->clientOptions['dataType'] = $this->dataType !== null ? $this->dataType : new JsExpression('ajaxHelper.getDataType(this)');
        $this->clientOptions['processData'] = $this->processData !== null ? $this->processData : new JsExpression('ajaxHelper.getProcessData(this)');
        $this->clientOptions['contentType'] = $this->contentType !== null ? $this->contentType : new JsExpression('ajaxHelper.getContentType(this)');

        //Following client options can only set in Ajax::begin(). Which means the following options are global settings for all
        //elements(with data-ajax attribute) during Ajax::begin() an Ajax::end.
        if($this->success !== null) $this->clientOptions['success'] = new JsExpression($this->success);
        if($this->error !== null) $this->clientOptions['error'] = new JsExpression($this->error);
        if($this->beforeSend !== null) $this->clientOptions['beforeSend'] = new JsExpression($this->beforeSend);
        if($this->complete !== null) $this->clientOptions['complete'] = new JsExpression($this->complete);
        if($this->cache !== null) $this->clientOptions['cache'] = $this->cache;
        if($this->timeout !== null) $this->clientOptions['timeout'] = $this->timeout;
        return Json::encode($this->clientOptions);
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $id = $this->options['id'];
        $options = $this->generateClientOptions();
        $linkSelector = $this->linkSelector !== null ? $this->linkSelector : '#' . $id . ' a[data-ajax]';
        $formSelector = $this->formSelector !== null ? $this->formSelector : '#' . $id . ' form[data-ajax]';
        $view = $this->getView();
        JqueryAsset::register($view);
        AjaxAsset::register($view);
        $js = "jQuery('$linkSelector').click(function() {var options=$options;ajaxHelper.filter(options);jQuery.ajax(options);return false;});";
        $js .= "\njQuery(document).on('submit', '$formSelector', function() {var options=$options;ajaxHelper.filter(options);jQuery.ajax(options);return false;});";
        $view->registerJs($js);
    }
}

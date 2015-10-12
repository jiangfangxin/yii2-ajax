# yii2-ajax
This is a ajax widget of yii2 which will generate a div. The tags (except form) click or forms submission (for those tags and forms with `data-ajax` attribute) in this div will trigger an ajax request.

## Simple example for link
We can get some data by ajax link useing Ajax widget. First, we should have a controller and an action to render our view. Of course, we can just use `SiteController` as our controller. Then we write an action in `SiteController` named `actionLink` to render link.php. In addition, add `actionResponse` action to response ajax request through ajax link:

SiteController.php
```php
<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

public function actionLink()
{
    return $this->render('link');
}

public function actionResponse()
{
    return 'Success, this is your data.';
}
?>
```

link.php
```php
<?php

use smallbearsoft\ajax\Ajax;
use yii\web\JsExpression;
use yii\helpers\Url;

Ajax::begin(['clientOptions' => [
    'success' => new JsExpression('function(data, textStatus, jqXHR) {alert(data)}'),
    'error' => new JsExpression('function(jqXHR, textStatus, errorThrown) {alert(errorThrown)}'),
    'beforeSend' => new JsExpression('function(jqXHR, settings) {alert("Before send.")}'),
    'complete' => new JsExpression('function(jqXHR, textStatus) {alert("Complete.")}'),
    'timeout' => 10000
]]) ?>
    <a href="<?= Url::to(['site/response']) ?>" data-ajax="1">This is an ajax link.</a>
<?php Ajax::end() ?>
```

## Simple example for form
If you want to use ajax to post a form to server, you can use this Ajax widget make it essay. We will still use `SiteController` as our controller. Then we add two actions `actionForm` and `actionPost`. To make it simple, we will not use `ActiveForm` widget, but you can use that in your code:

SiteController.php
```php
<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

public function actionForm()
{
    return $this->render('form');
}

public function actionPost()
{
    if(isset($_POST['name']) && isset($_POST['age'])) {
        $name = $_POST['name'];
        $age = $_POST['age'];
        return "Success, name is $name and age is $age.";
    } else {
        return 'Success, but we can not get the name and age.';
    }
}
?>
```

form.php
```php
<?php

use smallbearsoft\ajax\Ajax;
use yii\web\JsExpression;
use yii\helpers\Url;

Ajax::begin(['clientOptions' => [
    'success' => new JsExpression('function(data, textStatus, jqXHR) {alert(data)}'),
    'error' => new JsExpression('function(jqXHR, textStatus, errorThrown) {alert(errorThrown)}'),
    'beforeSend' => new JsExpression('function(jqXHR, settings) {alert("Before send.")}'),
    'complete' => new JsExpression('function(jqXHR, textStatus) {alert("Complete.")}'),
    'timeout' => 10000
]]) ?>
    <form action="<?= Url::to(['site/post'])?>" method="post" data-ajax="1">
        <input type="text" name="name" value="Fangxin Jiang"/>
        <input type="text" name="age" value="22"/>
        <input type="submit" value="Submit"/>
    </form>
<?php Ajax::end() ?>
```

Actually you can also use Ajax widget to upload files, just add an input like `<input type="file" name="image"/>`. You will see your file in `$_FILES` variable on server.

## More tutorials
See [Home wiki](https://github.com/smallbearsoft/yii2-ajax/wiki/Home) for more tutorials.

## Tips

To trigger the Ajax widget, you can use a submit button or a jquery submit() function. But cannot use the pure javascript submit() function to trigger it.
You can use:
```
<button type="submit">Submit</button> (In your form)
---OR---
$("#my-id").submit()
```
Cannot use:
```
document.getElementById("#my-id").submit()
```

## Installation
The preferred way to install this extension is through composer.

Either run

```
composer require smallbearsoft/yii2-ajax:*
```

or add

```
"smallbearsoft/yii2-ajax": "*"
```

to the require section of your composer.json file.

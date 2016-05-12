Создание собственных шаблонов
===========================

В каждом генераторе есть выпадающий список `Code Template`, который позволяет выбрать шаблон для генерирования кода.
По-умолчанию, у Gii есть только один шаблон - `default`, но Вы можете создать собственный, отвечающий Вашим запросам.

Если Вы откроете папку `@app\vendor\yiisoft\yii2-gii\generators`, Вы обнаружите там шесть папок генераторов.

```
+ controller
- crud
    + default
+ extension
+ form
+ model
+ module
```

Их имена - названия генераторов. Если Вы откроете любую из этих папок, Вы обнаружите там папку `default`, имя которой является именем шаблона.

Скопируйте папку `@app\vendor\yiisoft\yii2-gii\generators\crud\default` в другое место, например, `@app\myTemplates\crud\`.
Теперь откройте эту папку и измените шаблон в соответствии со своими задачами, например, добавьте `errorSummary` в представление `views\_form.php`:

```php
<?php
//...
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>
    <?= "<?=" ?> $form->errorSummary($model) ?> <!-- ДОБАВИТЬ СЮДА -->
//...
```

Теперь нужно сообщить Gii о нашем шаблоне. Настройте эти строки в конфигурационном файле:

```php
// config/web.php для basic-приложения
// ...
if (YII_ENV_DEV) {    
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',      
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],  
        'generators' => [ // здесь
            'crud' => [ // название генератора
                'class' => 'yii\gii\generators\crud\Generator', // класс генератора
                'templates' => [ // настройки сторонних шаблонов
                    'myCrud' => '@app/myTemplates/crud/default', // имя_шаблона => путь_к_шаблону
                ]
            ]
        ],
    ];
}
```

Откройте CRUD и Вы увидите, что в выпадающем списке `Code Template` появился Ваш собственный шаблон.

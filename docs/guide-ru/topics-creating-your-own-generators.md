Создание собственных генераторов
============================

Откройте папку любого генератора и Вы увидите два файла `form.php` и `Generator.php`.
Первый - это форма, второй - класс генератора. Для того, чтобы создать свой ??генератор,
Вам необходимо создать или переписать эти классы в какой-нибудь другой папке. Также, как
описано в предыдущем разделе, внесите изменения в конфигурацию:

```php
//config/web.php для basic-приложения
//..
if (YII_ENV_DEV) {    
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',      
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],  
         'generators' => [
            'myCrud' => [
                'class' => 'app\myTemplates\crud\Generator',
                'templates' => [
                    'my' => '@app/myTemplates/crud/default',
                ]
            ]
        ],
    ];
}
```

```php
// @app/myTemplates/crud/Generator.php
<?php
namespace app\myTemplates\crud;

class Generator extends \yii\gii\Generator
{
    public function getName()
    {
        return 'MY CRUD Generator';
    }

    public function getDescription()
    {
        return 'Мой crud-генератор. Такой же как и дефолтный, но зато мой...';
    }
    
    // ...
}
```

Откройте Gii Module и убедитесь, что в нем появился новый генератор.

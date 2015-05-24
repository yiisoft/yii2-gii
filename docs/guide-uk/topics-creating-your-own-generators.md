Створення власних генераторів
=============================

Відкрийте каталог будь-якого генератора й ви побачите два файли `form.php` і `Generator.php`.
Перший - це форма, другий - клас генератора. Для того, щоб створити ваш власний генератор, вам необхідно створити або
перевизначити ці класи у будь-якому каталозі. Знову, як і в попередньому розділі налаштуйте конфігурацію:

```php
// config/web.php у базовому додатку
// ...
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
        return 'My crud generator. The same as a native, but he is mine...';
    }
    
    // ...
}
```

Відкрийте модуль Gii та ви побачите, що в ньому з’явився новий генератор.

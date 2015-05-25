あなた自身のジェネレータを作成する
==================================

どれでもジェネレータのフォルダを開くと、`form.php` と `Generator.php` の二つのファイルがあります。
一つ目はフォームで、二番目のものがジェネレータクラスです。
あなた自身のジェネレータを作成するためには、これらのクラスをどこかのフォルダで作成またはオーバーライドする必要があります。
ここでも、前の節でしたのと同じように、構成情報をカスタマイズします。

```php
//config/web.php for basic app
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
        return 'MY CRUD ジェネレータ';
    }

    public function getDescription()
    {
        return 'My crud ジェネレータ。本来のものと同じだが、云々、、、';
    }
    
    // ...
}
```

Gii モジュールを開くと、新しいジェネレータがその中に出現します。

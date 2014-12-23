Gii Extension for Yii 2
========================

Gii 扩展为 Yii 2 应用程序提供了一个基于 Web 的代码生成器。
你可以使用 Gii 快速生成模型、表单、模块、CRUD等的代码。

*使用其他语言阅读: [English](README.md),  [简体中文](README.zh-CN.md).*

安装
------------

安装本扩展首选的方式是使用 [composer](http://getcomposer.org/download/)。

运行如下命令:

```
php composer.phar require --prefer-dist yiisoft/yii2-gii "*"
```

或将下面的代码添加到你的 `composer.json` 文件中。

```
"yiisoft/yii2-gii": "*"
```


使用方法
-----

安装完扩展只需要简单的在你的应用配置中做如下修改:


```php
return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
        // ...
    ],
    // ...
];
```

然后你可以使用下面的 URL 来访问 Gii:

```
http://localhost/path/to/index.php?r=gii
```

或者如果你启用了友好的 URL，你可以使用下面的 URL 来访问:

```
http://localhost/path/to/index.php/gii
```

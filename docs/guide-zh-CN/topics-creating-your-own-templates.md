创建自定义模版
===========================

每个生成器都有一个表单字段 `Code Template` ，它允许您选择一个模板用于代码生成。
默认情况下， Gii 只提供一个模板 `default` ，但你可以创建自己的模板，以适应不同的需求。

在文件夹 `@app\vendor\yiisoft\yii2-gii\generators` 下, 可以看到有6个文件夹，分别对应了不同的代码生成器。

```
+ controller
- crud
    + default
+ extension
+ form
+ model
+ module
```

文件夹的名字是生成器名称。 打开每一个文件夹，都可以看到 `default` 文件夹，这就是模版的名称。

将文件夹 `@app\vendor\yiisoft\yii2-gii\generators\crud\default` 复制到另一位置，例如 `@app\myTemplates\crud\`。
打开文件夹，并进行修改，例如： 在 `views\_form.php` 文件中添加 `errorSummary` :

```php
<?php
//...
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>
    <?= "<?=" ?> $form->errorSummary($model) ?> <!-- 在这里添加 -->
//...
```

然后修改配置文件，让 Gii 根据我们自己的模版进行代码生成：

```php
// config/web.php 基于 基础模版应用（ basic app ）
// ...
if (YII_ENV_DEV) {    
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',      
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],  
        'generators' => [ //这里配置生成器
            'crud' => [ // 生成器名称
                'class' => 'yii\gii\generators\crud\Generator', // 生成器类
                'templates' => [ //配置模版文件
                    'myCrud' => '@app/myTemplates/crud/default', // 模版名称 => 模版路径
                ]
            ]
        ],
    ];
}
```

打开 CRUD 生成器，你会看到在字段 `Code Template` 的表单中显示自己的模板。
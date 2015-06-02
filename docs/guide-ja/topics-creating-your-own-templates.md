あなた自身のテンプレートを作成する
==================================

すべてのジェネレータのフォームには `Code Template` というフィールドがあり、コード生成に使用するテンプレートを選択できるようになっています。
デフォルトでは、Gii は `default` という一つのテンプレートだけを提供しますが、あなたの要求を満たすように修正されたあなた自身のテンプレートを作成することも出来ます。

フォルダ `@app\vendor\yiisoft\yii2-gii\generators` を開くと、ジェネレータのフォルダが 6 つあるのに気づくでしょう。

```
+ controller
- crud
    + default
+ extension
+ form
+ model
+ module
```

これらはジェネレータの名前です。
どれでもフォルダを開くと、その中に `default` というフォルダがあります。
これがテンプレートの名前です。

フォルダ `@app\vendor\yiisoft\yii2-gii\generators\crud\default` を他の場所、例えば、`@app\myTemplates\crud\` にコピーします。
このフォルダを開いて、どれでもテンプレートをあなたの要求に合うように修正します。
例えば、`views\_form.php` に `errorSummary` を追加しましょう。

```php
<?php
//...
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>
    <?= "<?=" ?> $form->errorSummary($model) ?> <!-- これを追加 -->
//...
```

次に、Gii に私たちのテンプレートについて教える必要があります。
その設定は構成情報ファイルの中で行います。

```php
// config/web.php for basic app
// ...
if (YII_ENV_DEV) {    
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',      
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],  
        'generators' => [ // ここ
            'crud' => [ // ジェネレータの名前
                'class' => 'yii\gii\generators\crud\Generator', // ジェネレータクラス
                'templates' => [ //setting for out templates
                    'myCrud' => '@app/myTemplates/crud/default', // テンプレート名 => テンプレートへのパス
                ]
            ]
        ],
    ];
}
```

CRUD ジェネレータを開くと、フォームの `Code Template` のフィールドに、あなた自身のテンプレートが出現するようになっています。


Creating your own templates
===========================

Every generator has a form field `Code Template` that lets you choose a template to use for code generation.
By default Gii only provides one template `default` but you can create your own templates that are adjusted to your needs.

If you open the folder `@app\vendor\yiisoft\yii2-gii\generators`, you'll see six folders of generators.

```
+ controller
- crud
    + default
+ extension
+ form
+ model
+ module
```

These names are the generator names. If you open any of these folders, you can see the folder `default`, which name is the name of the template.

Copy the folder `@app\vendor\yiisoft\yii2-gii\generators\crud\default` to another location, for example `@app\myTemplates\crud\`.
Now open this folder and modify any template to fit your desires, for example, add `errorSummary` in `views\_form.php`:

```php
<?php
//...
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>
    <?= "<?=" ?> $form->errorSummary($model) ?> <!-- ADDED HERE -->
//...
```

Now you need to tell Gii about our template. The setting is made in the config file:

```php
// config/web.php for basic app
// ...
if (YII_ENV_DEV) {    
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',      
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],  
        'generators' => [ //here
            'crud' => [ // generator name
                'class' => 'yii\gii\generators\crud\Generator', // generator class
                'templates' => [ //setting for out templates
                    'myCrud' => '@app/myTemplates/crud/default', // template name => path to template
                ]
            ]
        ],
    ];
}
```

Open the CRUD generator and you will see that in the field `Code Template` of form appeared own template.

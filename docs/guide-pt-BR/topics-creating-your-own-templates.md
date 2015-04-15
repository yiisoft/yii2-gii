Criando seus próprios modelos (templates)
===========================

Cada gerador tem um campo no formulário chamado `Code Template` que permite que você escolha um modelo (template) a ser usado para geração de código.
Por padrão o Gii apenas fornece um template `default` mas você pode criar seus próprios modelos (templates).

Se você abrir a pasta `@app\vendor\yiisoft\yii2-gii\generators`, você verá seis pastas de geradores.

```
+ controller
- crud
    + default
+ extension
+ form
+ model
+ module
```

Esse são os nomes dos geradores. Se você abrir qualquer uma delas, você verá a pasta `default`, que é o nome do modelo (template).

Copie a pasta `@app\vendor\yiisoft\yii2-gii\generators\crud\default` para outro local, por exemplo `@app\myTemplates\crud\`.
Agora abra essa pasta e modifique qualquer modelo (template) para atenderem suas necessidades, por exemplo, adicione um `errorSummary` em `views\_form.php`:

```php
<?php
//...
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>
    <?= "<?=" ?> $form->errorSummary($model) ?> <!-- ADDED HERE -->
    <?php foreach ($safeAttributes as $attribute) {
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
    } ?>
//...
```

Agora você precisa informar ao Gii sobre nosso modelo (template). A configuração deve ser feita no arquivo de configuração:

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
Abra o gerador de CRUD e você verá  que no campo 'Code Template` do formulário aprece a opção do seu modelo (template).
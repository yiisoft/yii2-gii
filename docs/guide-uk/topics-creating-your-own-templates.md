Створення власних шаблонів
==========================

Кожний генератор має поле форми `Code Template` (`Шаблон коду`), яке дозволяє вам вибрати який шаблон використовувати для генерування коду.
За замовчуванням Gii надає лише один шаблон `default`, але ви можете створювати власні шаблони, які підходитимуть до ваших потреб.

Якщо ви відкриєте каталог `@app\vendor\yiisoft\yii2-gii\generators`, то побачите шість каталогів для генераторів.

```
+ controller
- crud
    + default
+ extension
+ form
+ model
+ module
```

Ці імена є іменами генераторів. Якщо відкриєте будь-який з цих каталогів, то зможете побачити каталог `default`, ім’я якого є ім’ям шаблона.

Скопіюйте каталог `@app\vendor\yiisoft\yii2-gii\generators\crud\default` в інше місце, наприклад, `@app\myTemplates\crud\`.
Тепер відкрийте цей каталог та змініть будь-який шаблон як забажаєте, наприклад, додайте `errorSummary` у `views\_form.php`:

```php
<?php
//...
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>
    <?= "<?=" ?> $form->errorSummary($model) ?> <!-- ДОДАНО ТУТ -->
//...
```

Тепер вам необхідно розповісти Gii про ваш шаблон. Налаштування робиться у файлі конфігурації:

```php
// config/web.php у базовому додатку
// ...
if (YII_ENV_DEV) {    
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',      
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],  
        'generators' => [ // тут
            'crud' => [ // ім’я генератора
                'class' => 'yii\gii\generators\crud\Generator', // клас генератора
                'templates' => [ // налаштування сторонніх шаблонів
                    'myCrud' => '@app/myTemplates/crud/default', // ім’я шаблону => шлях до шаблону
                ]
            ]
        ],
    ];
}
```

Відкрийте генератор CRUD й ви побачите, що у полі форми `Code Template` з’явився ваш власний шаблон.

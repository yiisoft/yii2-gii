Встановлення
============

## Встановлення через Composer

Рекомендується встановлювати це розширення за допомогою [Composer](http://getcomposer.org/download/).

Виконайте

```
php composer.phar require --dev --prefer-dist yiisoft/yii2-gii
```

або додайте

```
"yiisoft/yii2-gii": "~2.0.0"
```

до секції require-dev вашого файлу `composer.json`.


## Конфігурація додатка

Коли розширення Gii встановлено, для його підключення додайте наступні рядки до файлу конфігурації вашого додатка:

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

Потім ви можете мати доступ до Gii за наведеною URL-адресою:

```
http://localhost/path/to/index.php?r=gii
```

Якщо у вас налаштовані гарні URL-адреси, ви можете використовувати наступну адресу:

```
http://localhost/path/to/index.php/gii
```

> Примітка: якщо ви звертаєтеся до Gii з IP-адреси відмінної від localhost, доступ буде заборонений за замовчуванням.
> Для обходу цієї заборони, додайте дозволені IP-адреси до конфігурації:
>
```php
'gii' => [
    'class' => 'yii\gii\Module',
    'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'] // налаштувати для ваших потреб
],
```
Якщо ви так само налаштували Gii у конфігурації вашого консольного додатку, то можете також мати доступ до Gii з
вікна терміналу, як показано нижче:

```
# перейти до базової директорії додатку
cd path/to/AppBasePath

# показати допоміжну інформацію про Gii
yii help gii

# показати допоміжну інформацію про генератор моделі у Gii
yii help gii/model

# згенерувати модель City з таблиці city
yii gii/model --tableName=city --modelClass=City
```

### Базовий додаток

В базовому шаблоні проекту структура конфігурації трохи інакша, тому Gii потрібно сконфігурувати у
`config/web.php`:

```php
// ...
if (YII_ENV_DEV) {
    // налаштування конфігурації для середовища розробки
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module'; // <--- тут
}
```

Таким чином, щоб налаштувати IP-адреси, необхідно зробити як показано нижче:

```php
if (YII_ENV_DEV) {
    // налаштування конфігурації для середовища розробки
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],
    ];
}
```

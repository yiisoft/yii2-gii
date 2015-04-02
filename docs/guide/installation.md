Installation
============

## Getting Composer package

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --dev --prefer-dist yiisoft/yii2-gii
```

or add

```
"yiisoft/yii2-gii": "~2.0.0"
```

to the require-dev section of your `composer.json` file.


## Configuring application

Once the Gii extension has been installed, you enable it by adding these lines to your application configuration file:

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

You can then access Gii through the following URL:

```
http://localhost/path/to/index.php?r=gii
```

If you have enabled pretty URLs, you may use the following URL:

```
http://localhost/path/to/index.php/gii
```

> Note: if you are accessing gii from an IP address other than localhost, access will be denied by default.
> To circumvent that default, add the allowed IP addresses to the configuration:
>
```php
'gii' => [
    'class' => 'yii\gii\Module',
    'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'] // adjust this to your needs
],
```

If you have configured Gii similarly in your console application configuration, you may also access Gii through
command window like the following:

```
# change path to your application's base path
cd path/to/AppBasePath

# show help information about Gii
yii help gii

# show help information about the model generator in Gii
yii help gii/model

# generate City model from city table
yii gii/model --tableName=city --modelClass=City
```

### Basic application

In basic project template configuration structure is a bit different so Gii should be configured in
`config/web.php`:

```php
// ...
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module'; // <--- here
}
```

So in order to adjust IP address you need to do it like the following:

```php
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],
    ];
}
```

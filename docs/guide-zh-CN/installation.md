安装
============

## 获取 Composer 包

安装此扩展的首选方法是通过 [composer](http://getcomposer.org/download/).

执行

```
php composer.phar require --dev --prefer-dist yiisoft/yii2-gii
```

或者在项目的 `composer.json` 中的 require-dev 部分添加如下代码

```
"yiisoft/yii2-gii": "~2.0.0"
```


## 应用配置

一旦安装了 Gii 扩展，就可以通过将这些代码添加到应用程序配置文件来启用它：

```php
return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
        ],
        // ...
    ],
    // ...
];
```

然后，可以通过以下 URL 访问 Gii ：

```
http://localhost/path/to/index.php?r=gii
```

如果开启了 pretty URLs, 则这样访问:

```
http://localhost/path/to/index.php/gii
```

> 注意：如果从除 localhost 之外的 IP 地址访问 gii ，访问将被默认拒绝。
> 要规避该默认值，则需将允许的 IP 地址添加到配置中：
>
```php
'gii' => [
    'class' => 'yii\gii\Module',
    'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'] // adjust this to your needs
],
```

如果在控制台应用程序配置中对 Gii 做了类似的配置，那么还可以通过命令窗口访问Gii，如下所示：

```
# 切换至项目根路径
cd path/to/AppBasePath

# 查看 Gii 帮助信息
yii help gii

# 查看 Gii 中关于 model 生成器的帮助信息
yii help gii/model

# 基于 city 数据表生成 City model
yii gii/model --tableName=city --modelClass=City
```

### 基础项目模版（yii2-app-basic）

在基础项目模板中的配置结构有点不同，所以 Gii 应该在 `config/web.php` 文件中进行配置:

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

调整可被访问的 IP 地址则通过如下方式:

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
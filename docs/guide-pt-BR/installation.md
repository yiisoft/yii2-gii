Instalação
============

## Instalado o pocote composer

A maneira preferida para instalar essa extensão é via [composer](http://getcomposer.org/download/).

Então rode

```
php composer.phar require --dev --prefer-dist yiisoft/yii2-gii
```

ou adicione

```
"yiisoft/yii2-gii": "~2.0.0"
```

para ser requerido na sessão dev (desenvolvimento) do seu  arquivo `composer.json` .


## Configurando a Aplicação

Assim que a extensão Gii for instalada, você habilitá-la adicionando estas linhas no seu arquivo de configuração da aplicação:
	
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

Você pode acessar o Gii através da seguinte URL:

```
http://localhost/path/to/index.php?r=gii
```

Se você tiver habilitado as URLs amigavéis, você pode usar a seguinte URL:

```
http://localhost/path/to/index.php/gii
```

> Nota: Se você estiver acessando o gii a partir de um endereço IP diferente de localhost, o acesso será negado por padrão.
> Para contornar esse padrão, adicione permisão para o endereço de ip na configuração:
>

```php
'gii' => [
    'class' => 'yii\gii\Module',
    'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'] // adjust this to your needs
],
```

Se você tiver a configuração do Gii semelhante em sua configuração da aplicação console, você também pode acessar o Gii através
dos seguintes comandos:

```
# Altera o caminho para BasePath do sua aplicação
cd path/to/AppBasePath

# Mostra informações de ajuda do Gii
yii help gii

# Mostra informações de ajuda do gerador de modelos do Gii
yii help gii/model

# Gera modelo City a partir da tabela city
yii gii/model --tableName=city --modelClass=City
```

### Basic application (Aplicação básica)

Na estrutura de configuração de uma aplicação básica é um pouco diferente, o Gii deve ser configurado em
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

Portanto, a fim de ajustar o endereço IP que você precisa fazer o seguinte:

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

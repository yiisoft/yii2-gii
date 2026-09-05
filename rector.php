<?php

use MSpirkov\Yii2\Rector\Rules\AddPropertyTagsRector;
use MSpirkov\Yii2\Rector\Rules\RemoveRedundantPropertyTagsRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRules([
        AddPropertyTagsRector::class,
        RemoveRedundantPropertyTagsRector::class,
    ])
    ->withSkip([
        __DIR__ . '/src/generators/extension/default/AutoloadExample.php',
    ]);

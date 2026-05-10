<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\generators;

use yii\gii\Generator;

class ConcreteGenerator extends Generator
{
    public string $testClass = '';

    public function getName(): string
    {
        return 'Test Generator';
    }

    public function generate(): array
    {
        return [];
    }
}

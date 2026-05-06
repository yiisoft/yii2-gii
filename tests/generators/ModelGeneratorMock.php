<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\generators;

use yii\gii\generators\model\Generator;

/**
 * Just a mock for testing porpouses.
 *
 * @author Sidney Lins slinstj@gmail.com
 */
class ModelGeneratorMock extends Generator
{
    public function publicGenerateClassName($tableName, $useSchemaName = null): string
    {
        return $this->generateClassName($tableName, $useSchemaName);
    }
}

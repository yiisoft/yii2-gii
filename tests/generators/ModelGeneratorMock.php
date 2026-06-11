<?php

namespace yiiunit\gii\generators;

use yii\gii\generators\model\Generator;

/**
 * Just a mock for testing porpouses.
 *
 * @author Sidney Lins slinstj@gmail.com
 */
class ModelGeneratorMock extends Generator
{
    public function defaultTemplate()
    {
        return dirname(__DIR__, 2) . '/src/generators/model/default';
    }

    public function publicGenerateClassName($tableName, $useSchemaName = null)
    {
        return $this->generateClassName($tableName, $useSchemaName);
    }

    public function publicGenerateProperties($table)
    {
        return $this->generateProperties($table);
    }
}

<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\generators;

use Yii;
use yii\gii\generators\controller\Generator as ControllerGenerator;
use yii\helpers\FileHelper;
use yiiunit\gii\GiiTestCase;

/**
 * ControllerGeneratorTest checks that Gii controller generator produces valid results
 * @group gii
 */
class ControllerGeneratorTest extends GiiTestCase
{
    public function controllerData(): array
    {
        return [
            ['\app\controllers\ProductController', ['ProductController.php', 'index.php']],
            ['app\controllers\ProductController', ['ProductController.php', 'index.php']],
        ];
    }

    /**
     * @dataProvider controllerData
     */
    public function testSimpleWithNamespace($controllerClass, $expectedNames): void
    {
        FileHelper::createDirectory(Yii::getAlias('@app/controllers'));

        $generator = new ControllerGenerator();
        $generator->template = 'default';
        $generator->controllerClass = $controllerClass;

        $valid = $generator->validate();
        $this->assertTrue($valid, print_r($generator->getErrors(), true));

        $files = $generator->generate();
        $fileNames = array_map(static function ($f) {
            return basename($f->path);
        }, $files);
        sort($fileNames);
        $this->assertEquals($expectedNames, $fileNames);
    }
}

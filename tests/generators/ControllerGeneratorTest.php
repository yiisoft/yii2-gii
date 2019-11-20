<?php
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
    public function controllerData()
    {
        return [
            ['\app\runtime\controllers\ProductController', ['ProductController.php', 'index.php']],
            ['app\runtime\controllers\ProductController', ['ProductController.php', 'index.php']],
        ];
    }

    /**
     * @dataProvider controllerData
     */
    public function testSimpleWithNamespace($controllerClass, $expectedNames)
    {
        FileHelper::createDirectory(Yii::getAlias('@app/runtime/controllers'));

        $generator = new ControllerGenerator();
        $generator->template = 'default';
        $generator->controllerClass = $controllerClass;

        $valid = $generator->validate();
        $this->assertTrue($valid, print_r($generator->getErrors(), true));

        $files = $generator->generate();
        $fileNames = array_map(function ($f) {
            return basename($f->path);
        }, $files);
        sort($fileNames);
        $this->assertEquals($expectedNames, $fileNames);
    }
}

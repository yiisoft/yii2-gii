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

class ControllerGeneratorExtendedTest extends GiiTestCase
{
    public function testGetActionIDs(): void
    {
        $generator = new ControllerGenerator();
        $generator->actions = 'index, create, update';
        $this->assertEquals(['create', 'index', 'update'], $generator->getActionIDs());
    }

    public function testGetActionIDsWithSpaces(): void
    {
        $generator = new ControllerGenerator();
        $generator->actions = 'index  create   update';
        $this->assertEquals(['create', 'index', 'update'], $generator->getActionIDs());
    }

    public function testGetActionIDsWithDuplicates(): void
    {
        $generator = new ControllerGenerator();
        $generator->actions = 'index, create, index';
        $this->assertEquals(['create', 'index'], $generator->getActionIDs());
    }

    public function testGetActionIDsDefaultIsIndex(): void
    {
        $generator = new ControllerGenerator();
        $this->assertEquals(['index'], $generator->getActionIDs());
    }

    public function testGetControllerFile(): void
    {
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\SiteController';
        $expected = Yii::getAlias('@app/controllers/SiteController.php');
        $this->assertEquals($expected, $generator->getControllerFile());
    }

    public function testGetControllerID(): void
    {
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\SiteController';
        $this->assertEquals('site', $generator->getControllerID());
    }

    public function testGetControllerIDWithMultiWord(): void
    {
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\MyTestController';
        $this->assertEquals('my-test', $generator->getControllerID());
    }

    public function testGetControllerNamespace(): void
    {
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\admin\SiteController';
        $this->assertEquals('app\controllers\admin', $generator->getControllerNamespace());
    }

    public function testGetControllerSubPath(): void
    {
        $this->mockWebApplication();
        $app = Yii::$app;
        $this->assertNotNull($app);
        $app->controllerNamespace = 'app\controllers';
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\admin\SiteController';
        $this->assertEquals('admin/', $generator->getControllerSubPath());
    }

    public function testGetControllerSubPathNoSubPath(): void
    {
        $this->mockWebApplication();
        $app = Yii::$app;
        $this->assertNotNull($app);
        $app->controllerNamespace = 'app\controllers';
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\SiteController';
        $this->assertEquals('', $generator->getControllerSubPath());
    }

    public function testGetViewFileDefault(): void
    {
        $this->mockWebApplication();
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\SiteController';
        $generator->viewPath = '';
        $expected = Yii::getAlias('@app/views/site/index.php');
        $this->assertEquals($expected, $generator->getViewFile('index'));
    }

    public function testGetViewFileCustom(): void
    {
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\SiteController';
        $generator->viewPath = '@app/views/custom';
        $expected = Yii::getAlias('@app/views/custom/about.php');
        $this->assertEquals($expected, $generator->getViewFile('about'));
    }

    public function testValidateControllerClass(): void
    {
        FileHelper::createDirectory(Yii::getAlias('@app/controllers'));

        $generator = new ControllerGenerator();
        $generator->template = 'default';
        $generator->controllerClass = 'app\controllers\TestController';
        $this->assertTrue($generator->validate(), print_r($generator->getErrors(), true));
    }

    public function testValidateControllerClassInvalidName(): void
    {
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'NotEndingWithController';
        $generator->validate();
        $this->assertArrayHasKey('controllerClass', $generator->getErrors());
    }

    public function testGenerateMultipleActions(): void
    {
        FileHelper::createDirectory(Yii::getAlias('@app/controllers'));

        $generator = new ControllerGenerator();
        $generator->template = 'default';
        $generator->controllerClass = 'app\controllers\MultiActionController';
        $generator->actions = 'index, create, update';

        $valid = $generator->validate();
        $this->assertTrue($valid, print_r($generator->getErrors(), true));

        $files = $generator->generate();
        $this->assertCount(4, $files);
    }
}

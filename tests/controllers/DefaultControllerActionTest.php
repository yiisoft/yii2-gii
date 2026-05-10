<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\controllers;

use Yii;
use yii\gii\controllers\DefaultController;
use yii\gii\Module;
use yii\web\NotFoundHttpException;
use yiiunit\gii\TestCase;
use yiiunit\gii\generators\ConcreteGenerator;

class DefaultControllerActionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockWebApplication();
    }

    public function testActionPreviewThrowsForInvalidFile(): void
    {
        $generator = new ConcreteGenerator();

        $module = new Module('gii');
        $module->allowedIPs = ['*'];
        $module->generators = ['test' => $generator];
        Yii::$app->setModule('gii', $module);

        $controller = new DefaultController('default', $module);

        $this->expectException(NotFoundHttpException::class);
        $controller->actionPreview('test', 'invalid_file_id');
    }

    public function testActionDiffThrowsForInvalidFile(): void
    {
        $generator = new ConcreteGenerator();

        $module = new Module('gii');
        $module->allowedIPs = ['*'];
        $module->generators = ['test' => $generator];
        Yii::$app->setModule('gii', $module);

        $controller = new DefaultController('default', $module);

        $this->expectException(NotFoundHttpException::class);
        $controller->actionDiff('test', 'invalid_file_id');
    }

    public function testLoadGeneratorLoadsStickyAttributes(): void
    {
        $generator = new ConcreteGenerator();
        $module = new Module('gii');
        $module->allowedIPs = ['*'];
        $module->generators = ['test' => $generator];
        Yii::$app->setModule('gii', $module);

        $controller = new DefaultController('default', $module);

        $loaded = $this->invoke($controller, 'loadGenerator', ['test']);
        $this->assertInstanceOf(ConcreteGenerator::class, $loaded);
    }
}

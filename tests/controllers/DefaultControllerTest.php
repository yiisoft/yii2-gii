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
use yii\gii\Generator;
use yii\web\Controller;
use yiiunit\gii\TestCase;
use yiiunit\gii\generators\ConcreteGenerator;

class DefaultControllerTest extends TestCase
{
    private ?Module $module = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockWebApplication();
    }

    private function createModuleWithGenerator(): Module
    {
        $module = new Module('gii');
        $module->allowedIPs = ['*'];
        $module->generators = [
            'test' => new ConcreteGenerator(),
        ];
        return $module;
    }

    public function testBeforeActionSetsHtmlFormat(): void
    {
        $module = $this->createModuleWithGenerator();
        Yii::$app->setModule('gii', $module);

        $controller = new DefaultController('default', $module);
        $action = new \yii\base\Action('index', $controller);

        $controller->beforeAction($action);

        $this->assertEquals(\yii\web\Response::FORMAT_HTML, Yii::$app->response->format);
    }

    public function testLoadGeneratorThrowsForUnknownId(): void
    {
        $module = $this->createModuleWithGenerator();
        Yii::$app->setModule('gii', $module);

        $controller = new DefaultController('default', $module);

        $this->expectException(\yii\web\NotFoundHttpException::class);
        $this->expectExceptionMessage('Code generator not found');
        $this->invoke($controller, 'loadGenerator', ['nonexistent']);
    }

    public function testLoadGeneratorReturnsGeneratorForValidId(): void
    {
        $module = $this->createModuleWithGenerator();
        Yii::$app->setModule('gii', $module);

        $controller = new DefaultController('default', $module);

        $generator = $this->invoke($controller, 'loadGenerator', ['test']);
        $this->assertInstanceOf(ConcreteGenerator::class, $generator);
    }

    public function testControllerLayout(): void
    {
        $module = $this->createModuleWithGenerator();
        $controller = new DefaultController('default', $module);

        $this->assertEquals('generator', $controller->layout);
    }

    public function testActionActionThrowsForInvalidMethod(): void
    {
        $module = $this->createModuleWithGenerator();
        Yii::$app->setModule('gii', $module);

        $controller = new DefaultController('default', $module);

        $this->expectException(\yii\web\NotFoundHttpException::class);
        $controller->actionAction('test', 'nonexistent');
    }
}

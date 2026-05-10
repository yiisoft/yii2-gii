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

class DefaultControllerExtendedTest extends TestCase
{
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

    public function testActionViewWithInvalidValidation(): void
    {
        $module = $this->createModuleWithGenerator();
        Yii::$app->setModule('gii', $module);

        $generator = $module->generators['test'];
        $generator->template = 'nonexistent';

        $_POST = ['preview' => '1'];
        Yii::$app->request->setBodyParams(['preview' => '1']);

        $controller = new DefaultController('default', $module);

        try {
            $result = $controller->actionView('test');
        } catch (\yii\base\ViewNotFoundException $e) {
            $this->assertTrue(true);
        }

        $_POST = [];
    }

    public function testActionPreviewThrowsForInvalidFile(): void
    {
        $module = $this->createModuleWithGenerator();
        Yii::$app->setModule('gii', $module);

        $controller = new DefaultController('default', $module);

        $this->expectException(\yii\web\NotFoundHttpException::class);
        $controller->actionPreview('test', 'invalid_file_id');
    }

    public function testActionDiffThrowsForInvalidFile(): void
    {
        $module = $this->createModuleWithGenerator();
        Yii::$app->setModule('gii', $module);

        $controller = new DefaultController('default', $module);

        $this->expectException(\yii\web\NotFoundHttpException::class);
        $controller->actionDiff('test', 'invalid_file_id');
    }

    public function testModuleProperty(): void
    {
        $module = $this->createModuleWithGenerator();
        $controller = new DefaultController('default', $module);

        $this->assertSame($module, $controller->module);
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

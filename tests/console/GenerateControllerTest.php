<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\console;

use Yii;
use yii\gii\console\GenerateAction;
use yii\gii\console\GenerateController;
use yii\gii\Generator;
use yii\gii\Module;
use yiiunit\gii\TestCase;
use yiiunit\gii\generators\ConcreteGenerator;

class GenerateControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    public function testOptions(): void
    {
        $generator = new ConcreteGenerator();
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => ['test' => $generator],
        ]);

        $options = $controller->options('test');
        $this->assertContains('overwrite', $options);
        $this->assertContains('template', $options);
        $this->assertContains('enableI18N', $options);
        $this->assertContains('messageCategory', $options);
    }

    public function testOptionsForUnknownGenerator(): void
    {
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => [],
        ]);

        $options = $controller->options('nonexistent');
        $this->assertContains('overwrite', $options);
    }

    public function testActionsReturnsGeneratorActions(): void
    {
        $generator = new ConcreteGenerator();
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => ['test' => $generator],
        ]);

        $actions = $controller->actions();
        $this->assertArrayHasKey('test', $actions);
        $this->assertEquals(GenerateAction::class, $actions['test']['class']);
        $this->assertSame($generator, $actions['test']['generator']);
    }

    public function testGetUniqueID(): void
    {
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => [],
        ]);

        $this->assertEquals('gii', $controller->getUniqueID());
    }

    public function testGetActionHelpSummaryForGenerateAction(): void
    {
        $generator = new ConcreteGenerator();
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => ['test' => $generator],
        ]);

        $action = new GenerateAction('test', $controller, ['generator' => $generator]);
        $summary = $controller->getActionHelpSummary($action);
        $this->assertEquals('Test Generator', $summary);
    }

    public function testGetActionHelpSummaryForInlineAction(): void
    {
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => [],
        ]);

        $action = new \yii\base\InlineAction('index', $controller, 'actionIndex');
        $summary = $controller->getActionHelpSummary($action);
        $this->assertIsString($summary);
    }

    public function testGetActionHelpForGenerateAction(): void
    {
        $generator = new ConcreteGenerator();
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => ['test' => $generator],
        ]);

        $action = new GenerateAction('test', $controller, ['generator' => $generator]);
        $help = $controller->getActionHelp($action);
        $this->assertEquals('', $help);
    }

    public function testGetActionHelpForInlineAction(): void
    {
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => [],
        ]);

        $action = new \yii\base\InlineAction('index', $controller, 'actionIndex');
        $help = $controller->getActionHelp($action);
        $this->assertIsString($help);
    }

    public function testGetActionArgsHelpReturnsEmptyArray(): void
    {
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => [],
        ]);

        $this->assertEquals([], $controller->getActionArgsHelp(new \yii\base\InlineAction('test', $controller, 'test')));
    }

    public function testGetActionOptionsHelpForGenerateAction(): void
    {
        $generator = new ConcreteGenerator();
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => ['test' => $generator],
        ]);

        $action = new GenerateAction('test', $controller, ['generator' => $generator]);
        $help = $controller->getActionOptionsHelp($action);

        $this->assertArrayHasKey('overwrite', $help);
        $this->assertArrayHasKey('template', $help);
    }

    public function testGetActionOptionsHelpForInlineAction(): void
    {
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => [],
        ]);

        $action = new \yii\base\InlineAction('index', $controller, 'actionIndex');
        $help = $controller->getActionOptionsHelp($action);
        $this->assertIsArray($help);
    }

    public function testMagicGetSet(): void
    {
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => [],
        ]);

        $controller->someOption = 'testValue';
        $this->assertEquals('testValue', $controller->someOption);
        $this->assertNull($controller->nonexistentOption);
    }

    public function testInitCreatesGeneratorInstances(): void
    {
        $generator = new ConcreteGenerator();
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => ['test' => $generator],
        ]);
        $controller->init();

        $this->assertInstanceOf(ConcreteGenerator::class, $controller->generators['test']);
    }

    public function testCreateActionSetsOptions(): void
    {
        $generator = new ConcreteGenerator();
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => ['test' => $generator],
        ]);
        $controller->init();

        $controller->enableI18N = true;
        $action = $controller->createAction('test');

        $this->assertInstanceOf(GenerateAction::class, $action);
        $this->assertTrue($action->generator->enableI18N);
    }

    public function testFormatHint(): void
    {
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => [],
        ]);

        $result = $this->invoke($controller, 'formatHint', ['This is a <code>test</code> hint  with   spaces']);
        $this->assertStringNotContainsString('<code>', $result);
        $this->assertStringContainsString('test', $result);
    }

    public function testOverwriteProperty(): void
    {
        $controller = new GenerateController('gii', Yii::$app, [
            'generators' => [],
        ]);

        $this->assertFalse($controller->overwrite);
        $controller->overwrite = true;
        $this->assertTrue($controller->overwrite);
    }
}

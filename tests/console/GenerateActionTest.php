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
use yiiunit\gii\TestCase;
use yiiunit\gii\generators\ConcreteGenerator;

class GenerateActionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    private function createSilentController(array $generators = []): GenerateController
    {
        return new class('gii', Yii::$app, ['generators' => $generators]) extends GenerateController {
            public string $outputBuffer = '';

            public function stdout($string)
            {
                $this->outputBuffer .= $string;
                return strlen($string);
            }
        };
    }

    public function testRunWithValidationErrors(): void
    {
        $generator = new ConcreteGenerator();
        $generator->template = 'nonexistent';

        $consoleController = $this->createSilentController(['test' => $generator]);
        $consoleController->interactive = false;

        $action = new GenerateAction('test', $consoleController, ['generator' => $generator]);

        ob_start();
        $result = $action->run();
        ob_end_clean();
        $this->assertEquals(\yii\console\ExitCode::USAGE, $result);
    }

    public function testRunWithValidGenerator(): void
    {
        $generator = new ConcreteGenerator();

        $consoleController = $this->createSilentController(['test' => $generator]);
        $consoleController->interactive = false;
        $consoleController->overwrite = true;

        $action = new GenerateAction('test', $consoleController, ['generator' => $generator]);

        ob_start();
        $result = $action->run();
        ob_end_clean();

        $this->assertNull($result);
    }

    public function testDisplayValidationErrors(): void
    {
        $generator = new ConcreteGenerator();
        $generator->template = 'nonexistent';
        $generator->validate();

        $consoleController = $this->createSilentController(['test' => $generator]);

        $action = new GenerateAction('test', $consoleController, ['generator' => $generator]);

        $this->invoke($action, 'displayValidationErrors');

        $this->assertNotEmpty($generator->getErrors());
        $this->assertStringContainsString('Code not generated', $consoleController->outputBuffer);
    }

    public function testGenerateCodeWithNoFiles(): void
    {
        $generator = new ConcreteGenerator();

        $consoleController = $this->createSilentController(['test' => $generator]);
        $consoleController->interactive = false;

        $action = new GenerateAction('test', $consoleController, ['generator' => $generator]);

        ob_start();
        $this->invoke($action, 'generateCode');
        $output = ob_get_clean();

        $this->assertStringContainsString('No code', $output);
    }

    public function testRunOutput(): void
    {
        $generator = new ConcreteGenerator();

        $consoleController = $this->createSilentController(['test' => $generator]);
        $consoleController->interactive = false;
        $consoleController->overwrite = true;

        $action = new GenerateAction('test', $consoleController, ['generator' => $generator]);

        ob_start();
        $action->run();
        $output = ob_get_clean();

        $this->assertStringContainsString('Running', $output);
    }
}

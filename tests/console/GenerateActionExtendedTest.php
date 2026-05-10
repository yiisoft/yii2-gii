<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\console;

use Yii;
use yii\gii\CodeFile;
use yii\gii\console\GenerateAction;
use yii\gii\console\GenerateController;
use yiiunit\gii\TestCase;
use yiiunit\gii\generators\ConcreteGenerator;

class GenerateActionExtendedTest extends TestCase
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

    public function testGenerateCodeWithNewFiles(): void
    {
        $dir = Yii::getAlias('@app/runtime/gii_action_test_' . uniqid());
        @mkdir($dir, 0777, true);

        $generator = new class() extends ConcreteGenerator {
            public $testOutputDir = '';

            public function generate(): array
            {
                return [
                    new CodeFile($this->testOutputDir . '/TestFile.php', '<?php echo "test";'),
                ];
            }
        };
        $generator->testOutputDir = $dir;

        $consoleController = $this->createSilentController(['test' => $generator]);
        $consoleController->interactive = false;
        $consoleController->overwrite = true;

        $action = new GenerateAction('test', $consoleController, ['generator' => $generator]);

        ob_start();
        $this->invoke($action, 'generateCode');
        $output = ob_get_clean();

        $this->assertStringContainsString('[new]', $output);
        $this->assertStringContainsString('TestFile.php', $output);

        @unlink($dir . '/TestFile.php');
        @rmdir($dir);
    }

    public function testGenerateCodeWithUnchangedFiles(): void
    {
        $dir = Yii::getAlias('@app/runtime/gii_unchanged_test_' . uniqid());
        @mkdir($dir, 0777, true);
        $filePath = $dir . '/TestFile.php';
        file_put_contents($filePath, '<?php echo "test";');

        $generator = new class() extends ConcreteGenerator {
            public $testOutputDir = '';

            public function generate(): array
            {
                return [
                    new CodeFile($this->testOutputDir . '/TestFile.php', '<?php echo "test";'),
                ];
            }
        };
        $generator->testOutputDir = $dir;

        $consoleController = $this->createSilentController(['test' => $generator]);
        $consoleController->interactive = false;

        $action = new GenerateAction('test', $consoleController, ['generator' => $generator]);

        ob_start();
        $this->invoke($action, 'generateCode');
        $output = ob_get_clean();

        $this->assertStringContainsString('[unchanged]', $output);

        @unlink($filePath);
        @rmdir($dir);
    }

    public function testGenerateCodeWithChangedFiles(): void
    {
        $dir = Yii::getAlias('@app/runtime/gii_changed_test_' . uniqid());
        @mkdir($dir, 0777, true);
        $filePath = $dir . '/TestFile.php';
        file_put_contents($filePath, 'old content');

        $generator = new class() extends ConcreteGenerator {
            public $testOutputDir = '';

            public function generate(): array
            {
                return [
                    new CodeFile($this->testOutputDir . '/TestFile.php', 'new content'),
                ];
            }
        };
        $generator->testOutputDir = $dir;

        $consoleController = $this->createSilentController(['test' => $generator]);
        $consoleController->interactive = false;
        $consoleController->overwrite = true;

        $action = new GenerateAction('test', $consoleController, ['generator' => $generator]);

        ob_start();
        $this->invoke($action, 'generateCode');
        $output = ob_get_clean();

        $this->assertStringContainsString('[changed]', $output);

        @unlink($filePath);
        @rmdir($dir);
    }

    public function testRunCallsGenerateCodeWhenValid(): void
    {
        $generator = new ConcreteGenerator();

        $consoleController = $this->createSilentController(['test' => $generator]);
        $consoleController->interactive = false;
        $consoleController->overwrite = true;

        $action = new GenerateAction('test', $consoleController, ['generator' => $generator]);

        ob_start();
        $result = $action->run();
        $output = ob_get_clean();

        $this->assertStringContainsString('Running', $output);
        $this->assertNull($result);
    }

    public function testRunReturnsUsageWhenInvalid(): void
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
}

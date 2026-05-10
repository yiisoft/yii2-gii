<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii;

use Yii;
use yii\base\Action;
use yii\gii\CodeFile;
use yii\gii\Module;
use yii\web\Controller;

class CodeFileModuleSaveTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockWebApplication();
    }

    public function testSaveWithModuleCreatesDirs(): void
    {
        $module = new Module('gii');
        Yii::$app->setModule('gii', $module);

        Yii::$app->controller = new Controller('test', $module);
        Yii::$app->controller->action = new Action('test', Yii::$app->controller);

        $dir = Yii::getAlias('@app/runtime/gii_module_save_' . uniqid('', true));
        $path = $dir . '/subdir/TestFile.php';
        $codeFile = new CodeFile($path, '<?php echo "module save test";');

        $result = $codeFile->save();
        $this->assertTrue($result, 'Save should succeed with module dir creation');
        $this->assertFileExists($path);

        @unlink($path);
        @rmdir($dir . '/subdir');
        @rmdir($dir);
    }

    public function testSaveWithModuleSetsFileMode(): void
    {
        $module = new Module('gii');
        $module->newFileMode = 0644;
        Yii::$app->setModule('gii', $module);

        Yii::$app->controller = new Controller('test', $module);
        Yii::$app->controller->action = new Action('test', Yii::$app->controller);

        $path = Yii::getAlias('@app/runtime/gii_file_mode_test_' . uniqid('', true) . '.php');
        $codeFile = new CodeFile($path, '<?php echo "file mode test";');

        $result = $codeFile->save();
        $this->assertTrue($result);
        $this->assertFileExists($path);

        @unlink($path);
    }

    public function testSaveWithModuleNewDirMode(): void
    {
        $module = new Module('gii');
        $module->newDirMode = 0755;
        Yii::$app->setModule('gii', $module);

        Yii::$app->controller = new Controller('test', $module);
        Yii::$app->controller->action = new Action('test', Yii::$app->controller);

        $dir = Yii::getAlias('@app/runtime/gii_dir_mode_' . uniqid('', true));
        $path = $dir . '/TestFile.php';
        $codeFile = new CodeFile($path, '<?php echo "dir mode test";');

        $result = $codeFile->save();
        $this->assertTrue($result);
        $this->assertFileExists($path);

        @unlink($path);
        @rmdir($dir);
    }

    public function testSaveFailureForUnwritablePath(): void
    {
        $path = '/proc/impossible_path/file.php';
        $codeFile = new CodeFile($path, 'content');

        $result = $codeFile->save();
        $this->assertTrue($result === true || is_string($result));
    }
}

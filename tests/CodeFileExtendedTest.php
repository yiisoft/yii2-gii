<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii;

use Yii;
use yii\gii\CodeFile;
use yii\gii\Module;

class CodeFileExtendedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    public function testSaveWithModuleDirMode(): void
    {
        $module = new Module('gii');
        Yii::$app->setModule('gii', $module);

        $dir = Yii::getAlias('@app/runtime/gii_module_test_' . uniqid('', true));
        $path = $dir . '/TestFile.php';
        $codeFile = new CodeFile($path, '<?php echo "module test";');

        $result = $codeFile->save();
        $this->assertTrue($result);
        $this->assertFileExists($path);

        @unlink($path);
        @rmdir($dir);
    }

    public function testSaveOverwriteExistingFile(): void
    {
        $path = Yii::getAlias('@app/runtime/gii_overwrite_test_' . uniqid('', true) . '.php');
        file_put_contents($path, 'old content');

        $codeFile = new CodeFile($path, 'new content');
        $this->assertEquals(CodeFile::OP_OVERWRITE, $codeFile->operation);

        $result = $codeFile->save();
        $this->assertTrue($result);
        $this->assertEquals('new content', file_get_contents($path));

        @unlink($path);
    }

    public function testDiffForSkipOperation(): void
    {
        $path = Yii::getAlias('@app/runtime/gii_skip_diff_test_' . uniqid('', true) . '.txt');
        file_put_contents($path, 'same content');

        $codeFile = new CodeFile($path, 'same content');
        $this->assertEquals(CodeFile::OP_SKIP, $codeFile->operation);
        $this->assertEquals('', $codeFile->diff());

        @unlink($path);
    }

    public function testPreviewForCssFile(): void
    {
        $codeFile = new CodeFile('/path/to/file.css', 'body { color: red; }');
        $preview = $codeFile->preview();
        $this->assertIsString($preview);
        $this->assertStringContainsString('body', $preview);
    }

    public function testPreviewForJsFile(): void
    {
        $codeFile = new CodeFile('/path/to/file.js', 'alert("hello");');
        $preview = $codeFile->preview();
        $this->assertIsString($preview);
        $this->assertStringContainsString('alert', $preview);
    }

    public function testDiffForOverwriteWithActualContent(): void
    {
        $path = Yii::getAlias('@app/runtime/gii_diff_test_' . uniqid('', true) . '.txt');
        file_put_contents($path, "line1\nline2\nline3\n");

        $codeFile = new CodeFile($path, "line1\nmodified\nline3\n");
        $this->assertEquals(CodeFile::OP_OVERWRITE, $codeFile->operation);

        $diff = $codeFile->diff();
        $this->assertIsString($diff);
        $this->assertNotEmpty($diff);

        @unlink($path);
    }

    public function testGetTypeJson(): void
    {
        $codeFile = new CodeFile('/path/to/file.json', '{}');
        $this->assertEquals('json', $codeFile->getType());
    }

    public function testGetTypeCss(): void
    {
        $codeFile = new CodeFile('/path/to/file.css', 'body{}');
        $this->assertEquals('css', $codeFile->getType());
    }

    public function testPathNormalization(): void
    {
        $codeFile = new CodeFile('/some/path\\to/file.php', 'content');
        $this->assertStringNotContainsString('\\', $codeFile->path);
    }
}

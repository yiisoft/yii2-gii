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

class CodeFileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    public function testCreateOperationForNewFile(): void
    {
        $path = Yii::getAlias('@runtime/test_new_file_' . uniqid('', true) . '.php');
        $codeFile = new CodeFile($path, '<?php echo "hello";');

        $this->assertEquals(CodeFile::OP_CREATE, $codeFile->operation);
        $this->assertEquals($path, $codeFile->path);
        $this->assertEquals('<?php echo "hello";', $codeFile->content);
    }

    public function testSkipOperationForIdenticalFile(): void
    {
        $path = Yii::getAlias('@runtime/test_skip_' . uniqid('', true) . '.txt');
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, 'same content');

        $codeFile = new CodeFile($path, 'same content');
        $this->assertEquals(CodeFile::OP_SKIP, $codeFile->operation);

        @unlink($path);
    }

    public function testOverwriteOperationForDifferentFile(): void
    {
        $path = Yii::getAlias('@runtime/test_overwrite_' . uniqid('', true) . '.txt');
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, 'old content');

        $codeFile = new CodeFile($path, 'new content');
        $this->assertEquals(CodeFile::OP_OVERWRITE, $codeFile->operation);

        @unlink($path);
    }

    public function testSaveCreatesNewFile(): void
    {
        $dir = Yii::getAlias('@runtime/gii_test_' . uniqid('', true));
        $path = $dir . '/TestFile.php';
        $codeFile = new CodeFile($path, '<?php echo "test";');

        $result = $codeFile->save();
        $this->assertTrue($result);
        $this->assertFileExists($path);
        $this->assertEquals('<?php echo "test";', file_get_contents($path));

        @unlink($path);
        @rmdir($dir);
    }

    public function testSaveOverwritesExistingFile(): void
    {
        $dir = Yii::getAlias('@runtime/gii_test_' . uniqid('', true));
        @mkdir($dir, 0777, true);
        $path = $dir . '/TestFile.php';
        file_put_contents($path, 'old');

        $codeFile = new CodeFile($path, 'new content');
        $result = $codeFile->save();
        $this->assertTrue($result);
        $this->assertEquals('new content', file_get_contents($path));

        @unlink($path);
        @rmdir($dir);
    }

    public function testGetRelativePath(): void
    {
        $app = Yii::$app;
        $this->assertNotNull($app);
        $basePath = $app->basePath;
        $path = $basePath . '/models/Test.php';
        $codeFile = new CodeFile($path, '<?php');

        $this->assertEquals('models/Test.php', $codeFile->getRelativePath());
    }

    public function testGetRelativePathOutsideBasePath(): void
    {
        $path = '/some/outside/path/Test.php';
        $codeFile = new CodeFile($path, '<?php');

        $this->assertEquals($path, $codeFile->getRelativePath());
    }

    public function testGetTypePhp(): void
    {
        $codeFile = new CodeFile('/path/to/file.php', '<?php');
        $this->assertEquals('php', $codeFile->getType());
    }

    public function testGetTypeTxt(): void
    {
        $codeFile = new CodeFile('/path/to/file.txt', 'content');
        $this->assertEquals('txt', $codeFile->getType());
    }

    public function testGetTypeNoExtension(): void
    {
        $codeFile = new CodeFile('/path/to/Makefile', 'content');
        $this->assertEquals('unknown', $codeFile->getType());
    }

    public function testPreviewPhpFile(): void
    {
        $codeFile = new CodeFile('/path/to/file.php', '<?php echo "hello";');
        $preview = $codeFile->preview();
        $this->assertIsString($preview);
        $this->assertStringContainsString('hello', $preview);
    }

    public function testPreviewTextFile(): void
    {
        $codeFile = new CodeFile('/path/to/file.txt', 'plain text content');
        $preview = $codeFile->preview();
        $this->assertIsString($preview);
        $this->assertStringContainsString('plain text content', $preview);
    }

    public function testPreviewBinaryFileReturnsFalse(): void
    {
        $codeFile = new CodeFile('/path/to/image.jpg', 'binary data');
        $this->assertFalse($codeFile->preview());
    }

    public function testPreviewExeFileReturnsFalse(): void
    {
        $codeFile = new CodeFile('/path/to/app.exe', 'binary');
        $this->assertFalse($codeFile->preview());
    }

    public function testDiffForNewFileReturnsEmptyString(): void
    {
        $codeFile = new CodeFile('/path/to/new_file.txt', 'new content');
        $this->assertEquals('', $codeFile->diff());
    }

    public function testDiffForBinaryFileReturnsFalse(): void
    {
        $codeFile = new CodeFile('/path/to/image.png', 'binary');
        $this->assertFalse($codeFile->diff());
    }

    public function testDiffForOverwriteFile(): void
    {
        $path = Yii::getAlias('@runtime/test_diff_' . uniqid('', true) . '.txt');
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, "line1\nline2\n");

        $codeFile = new CodeFile($path, "line1\nline3\n");
        $this->assertEquals(CodeFile::OP_OVERWRITE, $codeFile->operation);

        $diff = $codeFile->diff();
        $this->assertIsString($diff);

        @unlink($path);
    }

    public function testIdIsMd5OfPath(): void
    {
        $path = '/some/path/file.php';
        $codeFile = new CodeFile($path, 'content');
        $this->assertEquals(md5($path), $codeFile->id);
    }
}

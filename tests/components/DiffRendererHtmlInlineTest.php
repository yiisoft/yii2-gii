<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\components;

use yii\gii\CodeFile;
use yiiunit\gii\TestCase;

class DiffRendererHtmlInlineTest extends TestCase
{
    public function testRenderViaCodeFile(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'gii_diff_test_');
        file_put_contents($path, "line1\nline2\nline3\n");

        $codeFile = new CodeFile($path, "line1\nmodified\nline3\n");
        $this->assertEquals(CodeFile::OP_OVERWRITE, $codeFile->operation);

        $diff = $codeFile->diff();
        $this->assertIsString($diff);
        $this->assertNotEmpty($diff);
        $this->assertStringContainsString('DifferencesInline', $diff);

        @unlink($path);
    }

    public function testRenderWithInsertViaCodeFile(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'gii_diff_ins_');
        file_put_contents($path, "line1\n");

        $codeFile = new CodeFile($path, "line1\nnew line\n");
        $diff = $codeFile->diff();
        $this->assertIsString($diff);
        $this->assertStringContainsString('ChangeInsert', $diff);

        @unlink($path);
    }

    public function testRenderWithReplaceViaCodeFile(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'gii_diff_rep_');
        file_put_contents($path, "old line\n");

        $codeFile = new CodeFile($path, "new line\n");
        $diff = $codeFile->diff();
        $this->assertIsString($diff);
        $this->assertStringContainsString('ChangeReplace', $diff);

        @unlink($path);
    }

    public function testRenderContainsTableStructure(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'gii_diff_tbl_');
        file_put_contents($path, "line1\n");

        $codeFile = new CodeFile($path, "line2\n");
        $diff = $codeFile->diff();
        $this->assertStringContainsString('<table', $diff);
        $this->assertStringContainsString('<thead>', $diff);
        $this->assertStringContainsString('<th>Old</th>', $diff);
        $this->assertStringContainsString('<th>New</th>', $diff);
        $this->assertStringContainsString('<th>Differences</th>', $diff);

        @unlink($path);
    }

    public function testRenderWithEqualAndChangeBlocks(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'gii_diff_eq_');
        file_put_contents($path, "line1\nline2\nline3\n");

        $codeFile = new CodeFile($path, "line1\nchanged\nline3\n");
        $diff = $codeFile->diff();
        $this->assertStringContainsString('ChangeEqual', $diff);
        $this->assertStringContainsString('ChangeReplace', $diff);

        @unlink($path);
    }

    public function testRenderMultipleChangesInSequence(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'gii_diff_seq_');
        file_put_contents($path, "a\nb\nc\nd\ne\nf\ng\n");

        $codeFile = new CodeFile($path, "a\nB\nc\nD\ne\nf\ng\n");
        $diff = $codeFile->diff();
        $this->assertIsString($diff);
        $this->assertNotEmpty($diff);

        @unlink($path);
    }
}

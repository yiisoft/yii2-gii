<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\generators;

use Yii;
use yii\gii\generators\extension\Generator as ExtensionGenerator;
use yiiunit\gii\TestCase;

class ExtensionGeneratorExtendedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    public function testSuccessMessage(): void
    {
        $generator = new ExtensionGenerator();
        $generator->vendorName = 'testvendor';
        $generator->packageName = 'yii2-test';
        $generator->namespace = 'testvendor\\';
        $message = $generator->successMessage();
        $this->assertStringContainsString('generated successfully', $message);
        $this->assertStringContainsString('git', $message);
    }

    public function testGenerateCreatesFiles(): void
    {
        $generator = new ExtensionGenerator();
        $generator->template = 'default';
        $generator->vendorName = 'testvendor';
        $generator->packageName = 'yii2-test';
        $generator->namespace = 'testvendor\\';
        $generator->license = 'MIT';
        $generator->title = 'Test Extension';
        $generator->description = 'Test extension description.';
        $generator->authorName = 'Test Author';
        $generator->authorEmail = 'test@example.com';

        $files = $generator->generate();
        $this->assertCount(3, $files);

        $fileNames = array_map(static function ($f) {
            return basename($f->path);
        }, $files);
        sort($fileNames);
        $this->assertContains('composer.json', $fileNames);
        $this->assertContains('AutoloadExample.php', $fileNames);
        $this->assertContains('README.md', $fileNames);
    }

    public function testAttributeLabels(): void
    {
        $generator = new ExtensionGenerator();
        $labels = $generator->attributeLabels();
        $this->assertArrayHasKey('vendorName', $labels);
        $this->assertArrayHasKey('packageName', $labels);
    }

    public function testHints(): void
    {
        $generator = new ExtensionGenerator();
        $hints = $generator->hints();
        $this->assertArrayHasKey('vendorName', $hints);
        $this->assertArrayHasKey('packageName', $hints);
        $this->assertArrayHasKey('namespace', $hints);
    }

    public function testValidateWithInvalidEmail(): void
    {
        $generator = new ExtensionGenerator();
        $generator->vendorName = 'test';
        $generator->packageName = 'yii2-test';
        $generator->namespace = 'test\\';
        $generator->license = 'MIT';
        $generator->title = 'Test';
        $generator->description = 'Test desc.';
        $generator->authorName = 'Test';
        $generator->authorEmail = 'not-an-email';

        $generator->validate();
        $this->assertArrayHasKey('authorEmail', $generator->getErrors());
    }

    public function testValidateWithInvalidVendorName(): void
    {
        $generator = new ExtensionGenerator();
        $generator->vendorName = 'INVALID';
        $generator->packageName = 'yii2-test';
        $generator->namespace = 'test\\';
        $generator->license = 'MIT';
        $generator->title = 'Test';
        $generator->description = 'Test desc.';
        $generator->authorName = 'Test';
        $generator->authorEmail = 'test@example.com';

        $generator->validate();
        $this->assertArrayHasKey('vendorName', $generator->getErrors());
    }

    public function testValidateWithInvalidNamespace(): void
    {
        $generator = new ExtensionGenerator();
        $generator->vendorName = 'test';
        $generator->packageName = 'yii2-test';
        $generator->namespace = 'no-trailing-slash';
        $generator->license = 'MIT';
        $generator->title = 'Test';
        $generator->description = 'Test desc.';
        $generator->authorName = 'Test';
        $generator->authorEmail = 'test@example.com';

        $generator->validate();
        $this->assertArrayHasKey('namespace', $generator->getErrors());
    }
}

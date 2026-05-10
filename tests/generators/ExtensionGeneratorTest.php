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

class ExtensionGeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    public function testGetName(): void
    {
        $generator = new ExtensionGenerator();
        $this->assertEquals('Extension Generator', $generator->getName());
    }

    public function testGetDescription(): void
    {
        $generator = new ExtensionGenerator();
        $this->assertNotEmpty($generator->getDescription());
    }

    public function testGetOutputPath(): void
    {
        $generator = new ExtensionGenerator();
        $this->assertEquals(Yii::getAlias('@app/runtime/tmp-extensions'), $generator->getOutputPath());
    }

    public function testGetKeywordsArrayJson(): void
    {
        $generator = new ExtensionGenerator();
        $generator->keywords = 'yii2,extension,test';
        $result = $generator->getKeywordsArrayJson();
        $decoded = json_decode($result, true);
        $this->assertEquals(['yii2', 'extension', 'test'], $decoded);
    }

    public function testGetKeywordsArrayJsonSingle(): void
    {
        $generator = new ExtensionGenerator();
        $generator->keywords = 'yii2';
        $result = $generator->getKeywordsArrayJson();
        $decoded = json_decode($result, true);
        $this->assertEquals(['yii2'], $decoded);
    }

    public function testOptsType(): void
    {
        $generator = new ExtensionGenerator();
        $types = $generator->optsType();
        $this->assertArrayHasKey('yii2-extension', $types);
        $this->assertArrayHasKey('library', $types);
    }

    public function testOptsLicense(): void
    {
        $generator = new ExtensionGenerator();
        $licenses = $generator->optsLicense();
        $this->assertArrayHasKey('MIT', $licenses);
        $this->assertArrayHasKey('Apache-2.0', $licenses);
        $this->assertArrayHasKey('BSD-3-Clause', $licenses);
        $this->assertArrayHasKey('GPL-3.0', $licenses);
    }

    public function testStickyAttributes(): void
    {
        $generator = new ExtensionGenerator();
        $sticky = $generator->stickyAttributes();
        $this->assertContains('vendorName', $sticky);
        $this->assertContains('outputPath', $sticky);
        $this->assertContains('authorName', $sticky);
        $this->assertContains('authorEmail', $sticky);
    }

    public function testRequiredTemplates(): void
    {
        $generator = new ExtensionGenerator();
        $this->assertEquals(['composer.json', 'AutoloadExample.php', 'README.md'], $generator->requiredTemplates());
    }

    public function testRules(): void
    {
        $generator = new ExtensionGenerator();
        $rules = $generator->rules();
        $this->assertNotEmpty($rules);
    }
}

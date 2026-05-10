<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\generators;

use Yii;
use yii\gii\generators\module\Generator as ModuleGenerator;
use yiiunit\gii\TestCase;

class ModuleGeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    public function testValidateModuleClassWithEmptyClass(): void
    {
        $generator = new ModuleGenerator();
        $generator->moduleClass = '';
        $generator->validateModuleClass();
        $this->assertArrayHasKey('moduleClass', $generator->getErrors());
    }

    public function testValidateModuleClassWithTrailingBackslash(): void
    {
        $generator = new ModuleGenerator();
        $generator->moduleClass = 'app\modules\test\\';
        $generator->validateModuleClass();
        $this->assertArrayHasKey('moduleClass', $generator->getErrors());
    }

    public function testValidateModuleClassWithNoNamespace(): void
    {
        $generator = new ModuleGenerator();
        $generator->moduleClass = 'Module';
        $generator->validateModuleClass();
        $this->assertArrayHasKey('moduleClass', $generator->getErrors());
    }

    public function testValidateModuleClassWithValidNamespace(): void
    {
        $generator = new ModuleGenerator();
        $generator->moduleClass = 'app\modules\admin\Module';
        $generator->validateModuleClass();
        $this->assertArrayNotHasKey('moduleClass', $generator->getErrors());
    }

    public function testGetModulePath(): void
    {
        $generator = new ModuleGenerator();
        $generator->moduleClass = 'app\modules\admin\Module';
        $this->assertEquals(Yii::getAlias('@app/modules/admin'), $generator->getModulePath());
    }

    public function testGetControllerNamespace(): void
    {
        $generator = new ModuleGenerator();
        $generator->moduleClass = 'app\modules\admin\Module';
        $this->assertEquals('app\modules\admin\controllers', $generator->getControllerNamespace());
    }

    public function testGetName(): void
    {
        $generator = new ModuleGenerator();
        $this->assertEquals('Module Generator', $generator->getName());
    }

    public function testGetDescription(): void
    {
        $generator = new ModuleGenerator();
        $this->assertNotEmpty($generator->getDescription());
    }

    public function testRequiredTemplates(): void
    {
        $generator = new ModuleGenerator();
        $this->assertEquals(['module.php', 'controller.php', 'view.php'], $generator->requiredTemplates());
    }

    public function testAttributeLabels(): void
    {
        $generator = new ModuleGenerator();
        $labels = $generator->attributeLabels();
        $this->assertArrayHasKey('moduleID', $labels);
        $this->assertArrayHasKey('moduleClass', $labels);
    }

    public function testHints(): void
    {
        $generator = new ModuleGenerator();
        $hints = $generator->hints();
        $this->assertArrayHasKey('moduleID', $hints);
        $this->assertArrayHasKey('moduleClass', $hints);
    }
}

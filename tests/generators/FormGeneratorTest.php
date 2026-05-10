<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\generators;

use yii\gii\generators\form\Generator as FormGenerator;
use yiiunit\gii\GiiTestCase;

class FormGeneratorTest extends GiiTestCase
{
    public function testGetName(): void
    {
        $generator = new FormGenerator();
        $this->assertEquals('Form Generator', $generator->getName());
    }

    public function testGetDescription(): void
    {
        $generator = new FormGenerator();
        $this->assertNotEmpty($generator->getDescription());
    }

    public function testRequiredTemplates(): void
    {
        $generator = new FormGenerator();
        $this->assertEquals(['form.php', 'action.php'], $generator->requiredTemplates());
    }

    public function testStickyAttributes(): void
    {
        $generator = new FormGenerator();
        $sticky = $generator->stickyAttributes();
        $this->assertContains('viewPath', $sticky);
        $this->assertContains('scenarioName', $sticky);
    }

    public function testValidateViewPathWithInvalidPath(): void
    {
        $generator = new FormGenerator();
        $generator->viewPath = '/nonexistent/path/that/does/not/exist';
        $generator->validateViewPath();
        $this->assertArrayHasKey('viewPath', $generator->getErrors());
    }

    public function testValidateViewPathWithValidPath(): void
    {
        $generator = new FormGenerator();
        $generator->viewPath = '@runtime';
        $generator->validateViewPath();
        $this->assertArrayNotHasKey('viewPath', $generator->getErrors());
    }

    public function testGetModelAttributes(): void
    {
        $generator = new FormGenerator();
        $generator->modelClass = 'yiiunit\gii\Profile';
        $attributes = $generator->getModelAttributes();
        $this->assertNotEmpty($attributes);
        $this->assertContains('description', $attributes);
    }

    public function testGetModelAttributesWithScenario(): void
    {
        $generator = new FormGenerator();
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->scenarioName = 'default';
        $attributes = $generator->getModelAttributes();
        $this->assertNotEmpty($attributes);
    }

    public function testAttributeLabels(): void
    {
        $generator = new FormGenerator();
        $labels = $generator->attributeLabels();
        $this->assertArrayHasKey('modelClass', $labels);
        $this->assertArrayHasKey('viewName', $labels);
        $this->assertArrayHasKey('viewPath', $labels);
        $this->assertArrayHasKey('scenarioName', $labels);
    }
}

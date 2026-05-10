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

class FormGeneratorExtendedTest extends GiiTestCase
{
    public function testSuccessMessage(): void
    {
        $generator = new FormGenerator();
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->viewName = 'profile';
        $generator->viewPath = '@app/runtime';

        $message = $generator->successMessage();
        $this->assertStringContainsString('generated successfully', $message);
    }

    public function testGenerateCreatesFile(): void
    {
        $generator = new FormGenerator();
        $generator->template = 'default';
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->viewName = 'profile';
        $generator->viewPath = '@app/runtime';

        $files = $generator->generate();
        $this->assertCount(1, $files);
        $this->assertStringContainsString('profile.php', basename($files[0]->path));
    }

    public function testRules(): void
    {
        $generator = new FormGenerator();
        $rules = $generator->rules();
        $this->assertNotEmpty($rules);
    }

    public function testValidateModelClassInvalid(): void
    {
        $generator = new FormGenerator();
        $generator->modelClass = 'NonExistentClass';
        $generator->validate();

        $this->assertArrayHasKey('modelClass', $generator->getErrors());
    }
}

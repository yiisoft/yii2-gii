<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\components;

use yii\gii\components\ActiveField;
use yii\helpers\FileHelper;
use yii\widgets\ActiveForm;
use yiiunit\gii\TestCase;
use yiiunit\gii\generators\ConcreteGenerator;

class ActiveFieldFullTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockWebApplication([
            'components' => [
                'assetManager' => [
                    'basePath' => __DIR__ . '/runtime/assets',
                    'baseUrl' => '/assets',
                    'bundles' => false,
                ],
            ],
        ]);
        FileHelper::createDirectory(__DIR__ . '/runtime/assets');
    }

    protected function tearDown(): void
    {
        FileHelper::removeDirectory(__DIR__ . '/runtime');
        parent::tearDown();
    }

    public function testCheckboxRendersWithForm(): void
    {
        $generator = new ConcreteGenerator();
        ob_start();
        $form = ActiveForm::begin(['action' => '/test', 'fieldClass' => ActiveField::class]);
        $field = $form->field($generator, 'enableI18N');
        ActiveForm::end();
        ob_end_clean();

        $field->checkbox([], false);

        $this->assertEquals("{input}\n{label}\n{error}", $field->template);
        $this->assertStringContainsString('form-check', $field->options['class']);
        $this->assertStringContainsString('form-check-label', $field->labelOptions['class']);
    }

    public function testRadioRendersWithForm(): void
    {
        $generator = new ConcreteGenerator();
        ob_start();
        $form = ActiveForm::begin(['action' => '/test', 'fieldClass' => ActiveField::class]);
        $field = $form->field($generator, 'enableI18N');
        ActiveForm::end();
        ob_end_clean();

        $field->radio([], false);

        $this->assertEquals("{input}\n{label}\n{error}", $field->template);
        $this->assertStringContainsString('form-check', $field->options['class']);
        $this->assertStringContainsString('form-check-label', $field->labelOptions['class'] ?? '');
    }
}

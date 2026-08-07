<?php

declare(strict_types=1);

namespace yiiunit\gii;

use yii\gii\components\ActiveField;
use yii\gii\generators\controller\Generator as ControllerGenerator;
use yii\widgets\ActiveForm;

/**
 * Unit tests for {@see \yii\gii\components\ActiveField} checkbox and radio Bootstrap field rendering.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 22.0
 */
class ActiveFieldTest extends TestCase
{
    private ActiveField $field;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();

        $generator = new ControllerGenerator();
        $form = new ActiveForm(['action' => '/test', 'id' => 'test-form']);
        ob_get_clean(); // close buffer opened by ActiveForm::init()

        $this->field = new ActiveField([
            'form' => $form,
            'model' => $generator,
            'attribute' => 'template',
        ]);
    }

    public function testCheckboxSetsTemplate(): void
    {
        $this->field->checkbox();

        self::assertSame(
            "{input}\n{label}\n{error}",
            $this->field->template,
            'Template must be input→label→error.',
        );
    }

    public function testCheckboxSetsFormCheckContainerClass(): void
    {
        $this->field->checkbox();

        self::assertStringContainsString(
            'form-check',
            $this->field->options['class'] ?? '',
            '`form-check` class must be on the field container.',
        );
    }

    public function testCheckboxSetsFormCheckLabelClassInLabelOptions(): void
    {
        $this->field->checkbox();

        self::assertStringContainsString(
            'form-check-label',
            $this->field->labelOptions['class'] ?? '',
            '`form-check-label` must be in labelOptions.',
        );
    }

    public function testCheckboxLabelPartNotPreset(): void
    {
        $this->field->checkbox();

        self::assertArrayNotHasKey(
            '{label}',
            $this->field->parts,
            'Label part must remain unset so render() generates it from labelOptions.',
        );
    }

    public function testCheckboxInputContainsFormCheckInputClass(): void
    {
        $this->field->checkbox();

        self::assertStringContainsString(
            'form-check-input',
            $this->field->parts['{input}'] ?? '',
            '`form-check-input` must be on the checkbox input.',
        );
    }

    public function testRadioSetsTemplate(): void
    {
        $this->field->radio();

        self::assertSame(
            "{input}\n{label}\n{error}",
            $this->field->template,
            'Template must be input→label→error.',
        );
    }

    public function testRadioSetsFormCheckContainerClass(): void
    {
        $this->field->radio();

        self::assertStringContainsString(
            'form-check',
            $this->field->options['class'] ?? '',
            '`form-check` class must be on the field container.',
        );
    }

    public function testRadioSetsFormCheckLabelClassInLabelOptions(): void
    {
        $this->field->radio();

        self::assertStringContainsString(
            'form-check-label',
            $this->field->labelOptions['class'] ?? '',
            '`form-check-label` must be in labelOptions.',
        );
    }

    public function testRadioLabelPartNotPreset(): void
    {
        $this->field->radio();

        self::assertArrayNotHasKey(
            '{label}',
            $this->field->parts,
            'Label part must remain unset so render() generates it from labelOptions.',
        );
    }

    public function testRadioInputContainsFormCheckInputClass(): void
    {
        $this->field->radio();

        self::assertStringContainsString(
            'form-check-input',
            $this->field->parts['{input}'] ?? '',
            '`form-check-input` must be on the radio input.',
        );
    }
}

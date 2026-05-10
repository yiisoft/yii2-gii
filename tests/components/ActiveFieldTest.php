<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\components;

use Yii;
use yii\gii\components\ActiveField;
use yii\helpers\Html;
use yiiunit\gii\TestCase;
use yiiunit\gii\generators\ConcreteGenerator;

class ActiveFieldTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockWebApplication();
    }

    private function createField(string $attribute): ActiveField
    {
        $generator = new ConcreteGenerator();

        $field = new ActiveField([
            'model' => $generator,
            'attribute' => $attribute,
        ]);

        return $field;
    }

    public function testStickyAddsCssClass(): void
    {
        $field = $this->createField('template');
        $result = $field->sticky();

        $this->assertSame($field, $result);
        $this->assertStringContainsString('sticky', $field->options['class']);
    }

    public function testAutoCompleteSetsListPart(): void
    {
        $field = $this->createField('testClass');
        $result = $field->autoComplete(['option1', 'option2']);

        $this->assertSame($field, $result);
        $this->assertStringContainsString('datalist', $field->parts['{list}']);
        $this->assertStringContainsString('option1', $field->parts['{list}']);
        $this->assertStringContainsString('option2', $field->parts['{list}']);
    }

    public function testHintSetsLabelOptions(): void
    {
        $field = $this->createField('testClass');
        $result = $field->hint('Test hint content');

        $this->assertSame($field, $result);
        $this->assertStringContainsString('help', $field->labelOptions['class']);
    }

    public function testCheckboxTemplateAndCssClassBehavior(): void
    {
        $generator = new ConcreteGenerator();
        $field = new ActiveField(['model' => $generator, 'attribute' => 'enableI18N']);

        $options = [];
        Html::addCssClass($field->options, 'form-check');
        Html::addCssClass($options, 'form-check-input');
        Html::addCssClass($field->labelOptions, 'form-check-label');
        $field->template = "{input}\n{label}\n{error}";

        $this->assertEquals("{input}\n{label}\n{error}", $field->template);
        $this->assertStringContainsString('form-check', $field->options['class']);
        $this->assertStringContainsString('form-check-label', $field->labelOptions['class']);
        $this->assertStringContainsString('form-check-input', implode(' ', $options));
    }

    public function testRadioTemplateAndCssClassBehavior(): void
    {
        $generator = new ConcreteGenerator();
        $field = new ActiveField(['model' => $generator, 'attribute' => 'enableI18N']);

        $options = [];
        Html::addCssClass($field->options, 'form-check');
        Html::addCssClass($options, 'form-check-input');
        Html::addCssClass($field->labelOptions, 'form-check-label');
        $field->template = "{input}\n{label}\n{error}";

        $this->assertEquals("{input}\n{label}\n{error}", $field->template);
        $this->assertStringContainsString('form-check', $field->options['class']);
        $this->assertStringContainsString('form-check-label', $field->labelOptions['class']);
    }

    public function testTemplateContainsListPlaceholder(): void
    {
        $field = $this->createField('testClass');
        $this->assertStringContainsString('{list}', $field->template);
    }

    public function testInitSetsStickyForStickyAttribute(): void
    {
        $generator = new ConcreteGenerator();
        $field = new ActiveField([
            'model' => $generator,
            'attribute' => 'template',
        ]);
        $field->init();
        $this->assertStringContainsString('sticky', $field->options['class']);
    }

    public function testInitDoesNotSetStickyForNonStickyAttribute(): void
    {
        $generator = new ConcreteGenerator();
        $field = new ActiveField([
            'model' => $generator,
            'attribute' => 'testClass',
        ]);
        $field->init();
        $this->assertStringNotContainsString('sticky', $field->options['class'] ?? '');
    }

    public function testInitSetsEmptyListForNoAutoComplete(): void
    {
        $generator = new ConcreteGenerator();
        $field = new ActiveField([
            'model' => $generator,
            'attribute' => 'testClass',
        ]);
        $field->init();
        $this->assertEquals('', $field->parts['{list}']);
    }

    public function testInitSetsHintForHintedAttribute(): void
    {
        $generator = new ConcreteGenerator();
        $field = new ActiveField([
            'model' => $generator,
            'attribute' => 'enableI18N',
        ]);
        $field->init();
        $this->assertStringContainsString('help', $field->labelOptions['class']);
    }
}

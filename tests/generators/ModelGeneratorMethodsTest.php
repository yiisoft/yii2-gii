<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\generators;

use Yii;
use yii\gii\generators\model\Generator as ModelGenerator;
use yiiunit\gii\GiiTestCase;

class ModelGeneratorMethodsTest extends GiiTestCase
{
    public function testGeneratePropertiesCoversAllTypes(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'product';

        $db = Yii::$app->db;
        $table = $db->getTableSchema('product');

        $properties = $this->invoke($generator, 'generateProperties', [$table]);
        $this->assertNotEmpty($properties);
        $this->assertArrayHasKey('id', $properties);
        $this->assertEquals('int', $properties['id']['type']);
    }

    public function testGeneratePropertiesCoversNullableColumns(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'product';

        $db = Yii::$app->db;
        $table = $db->getTableSchema('product');

        $properties = $this->invoke($generator, 'generateProperties', [$table]);
        $this->assertNotEmpty($properties);

        $foundNullable = false;
        foreach ($properties as $prop) {
            if (strpos($prop['type'], '|null') !== false) {
                $foundNullable = true;
                break;
            }
        }
        $this->assertTrue($foundNullable, 'Should have nullable properties');
    }

    public function testGenerateClassNameResolutionWithClassConstant(): void
    {
        $generator = new ModelGenerator();
        $generator->useClassConstant = true;
        $result = $this->invoke($generator, 'generateClassNameResolution', ['Profile']);
        $this->assertEquals('Profile::class', $result);
    }

    public function testGenerateClassNameResolutionWithoutClassConstant(): void
    {
        $generator = new ModelGenerator();
        $generator->useClassConstant = false;
        $result = $this->invoke($generator, 'generateClassNameResolution', ['Profile']);
        $this->assertEquals('Profile::className()', $result);
    }

    public function testGenerateRelationLink(): void
    {
        $generator = new ModelGenerator();
        $result = $this->invoke($generator, 'generateRelationLink', [['id' => 'category_id']]);
        $this->assertEquals("['id' => 'category_id']", $result);
    }

    public function testGenerateRelationLinkMultiple(): void
    {
        $generator = new ModelGenerator();
        $result = $this->invoke($generator, 'generateRelationLink', [['id1' => 'fk1', 'id2' => 'fk2']]);
        $this->assertStringContainsString("'id1' => 'fk1'", $result);
        $this->assertStringContainsString("'id2' => 'fk2'", $result);
    }

    public function testIsColumnAutoIncrementalWithNonAutoIncrement(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';

        $db = Yii::$app->db;
        $table = $db->getTableSchema('profile');

        $result = $this->invoke($generator, 'isColumnAutoIncremental', [$table, ['description']]);
        $this->assertFalse($result);
    }

    public function testCheckJunctionTableWithNonJunctionTable(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';

        $db = Yii::$app->db;
        $table = $db->getTableSchema('profile');

        $result = $this->invoke($generator, 'checkJunctionTable', [$table]);
        $this->assertFalse($result);
    }

    public function testGetEnumForNonEnumColumns(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';

        $db = Yii::$app->db;
        $table = $db->getTableSchema('profile');

        $enum = $generator->getEnum($table->columns);
        $this->assertEmpty($enum);
    }

    public function testValidateTableNameWithAsterisk(): void
    {
        $generator = new ModelGenerator();
        $generator->tableName = 'profile*';
        $generator->validateTableName();
        $this->assertArrayHasKey('tableName', $generator->getErrors());
    }

    public function testValidateModelClassEmptyWithAsteriskTableName(): void
    {
        $generator = new ModelGenerator();
        $generator->tableName = '*';
        $generator->modelClass = '';
        $generator->validateModelClass();
        $this->assertArrayNotHasKey('modelClass', $generator->getErrors());
    }

    public function testGenerateWithTablePrefix(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';
        $generator->useTablePrefix = true;

        $files = $generator->generate();
        $this->assertNotEmpty($files);
    }

    public function testGenerateWithoutRelations(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';
        $generator->generateRelations = ModelGenerator::RELATIONS_NONE;

        $files = $generator->generate();
        $this->assertNotEmpty($files);
    }

    public function testGenerateWithInverseRelations(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';
        $generator->generateRelations = ModelGenerator::RELATIONS_ALL_INVERSE;

        $files = $generator->generate();
        $this->assertNotEmpty($files);
    }

    public function testGetTableNamesWithSpecificTable(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';

        $names = $this->invoke($generator, 'getTableNames');
        $this->assertContains('profile', $names);
    }

    public function testGetTableNamesWithPattern(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'pro*';

        $names = $this->invoke($generator, 'getTableNames');
        $this->assertNotEmpty($names);
    }

    public function testGenerateLabelsFromComments(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';
        $generator->generateLabelsFromComments = true;

        $db = Yii::$app->db;
        $table = $db->getTableSchema('profile');
        $labels = $generator->generateLabels($table);

        $this->assertNotEmpty($labels);
        $this->assertArrayHasKey('id', $labels);
    }

    public function testGenerateRulesWithTableSchema(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';

        $db = Yii::$app->db;
        $table = $db->getTableSchema('profile');
        $rules = $generator->generateRules($table);

        $this->assertNotEmpty($rules);
    }
}

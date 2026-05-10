<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\generators;

use Yii;
use yii\db\ColumnSchema;
use yii\gii\generators\model\Generator as ModelGenerator;
use yiiunit\gii\GiiTestCase;

class ModelGeneratorExtendedTest extends GiiTestCase
{
    public function testValidateDbWithInvalidComponent(): void
    {
        $generator = new ModelGenerator();
        $generator->db = 'nonexistent';
        $generator->validateDb();
        $this->assertArrayHasKey('db', $generator->getErrors());
    }

    public function testValidateDbWithNonDbComponent(): void
    {
        $generator = new ModelGenerator();
        Yii::$app->set('notdb', ['class' => 'yii\web\Request']);
        $generator->db = 'notdb';
        $generator->validateDb();
        $this->assertArrayHasKey('db', $generator->getErrors());
    }

    public function testValidateDbWithValidDb(): void
    {
        $generator = new ModelGenerator();
        $generator->db = 'db';
        $generator->validateDb();
        $this->assertArrayNotHasKey('db', $generator->getErrors());
    }

    public function testValidateNamespaceWithInvalidNs(): void
    {
        $generator = new ModelGenerator();
        $generator->ns = 'nonexistent\namespace';
        $generator->validateNamespace('ns');
        $this->assertArrayHasKey('ns', $generator->getErrors());
    }

    public function testValidateNamespaceWithValidNs(): void
    {
        $generator = new ModelGenerator();
        $generator->ns = 'app\models';
        $generator->validateNamespace('ns');
        $this->assertArrayNotHasKey('ns', $generator->getErrors());
    }

    public function testValidateModelClassWithReservedKeyword(): void
    {
        $generator = new ModelGenerator();
        $generator->modelClass = 'class';
        $generator->tableName = 'some_table';
        $generator->validateModelClass();
        $this->assertArrayHasKey('modelClass', $generator->getErrors());
    }

    public function testValidateModelClassEmptyWithNonStarTable(): void
    {
        $generator = new ModelGenerator();
        $generator->modelClass = '';
        $generator->tableName = 'some_table';
        $generator->validateModelClass();
        $this->assertArrayHasKey('modelClass', $generator->getErrors());
    }

    public function testValidateModelClassWithValidClass(): void
    {
        $generator = new ModelGenerator();
        $generator->modelClass = 'SomeModel';
        $generator->tableName = 'some_table';
        $generator->validateModelClass();
        $this->assertArrayNotHasKey('modelClass', $generator->getErrors());
    }

    public function testValidateTableNameWithAsteriskNotAtEnd(): void
    {
        $generator = new ModelGenerator();
        $generator->tableName = 'tbl_*_something';
        $generator->validateTableName();
        $this->assertArrayHasKey('tableName', $generator->getErrors());
    }

    public function testValidateTableNameNonExistent(): void
    {
        $generator = new ModelGenerator();
        $generator->tableName = 'nonexistent_table_xyz';
        $generator->validateTableName();
        $this->assertArrayHasKey('tableName', $generator->getErrors());
    }

    public function testValidateTableNameValid(): void
    {
        $generator = new ModelGenerator();
        $generator->tableName = 'profile';
        $generator->validateTableName();
        $this->assertArrayNotHasKey('tableName', $generator->getErrors());
    }

    public function testGenerateTableNameWithoutPrefix(): void
    {
        $generator = new ModelGenerator();
        $generator->useTablePrefix = false;
        $this->assertEquals('profile', $generator->generateTableName('profile'));
    }

    public function testGenerateTableNameWithPrefix(): void
    {
        $generator = new ModelGenerator();
        $generator->useTablePrefix = true;

        $db = Yii::$app->db;
        $db->tablePrefix = 'tbl_';
        $result = $generator->generateTableName('tbl_profile');
        $this->assertEquals('{{%profile}}', $result);
        $db->tablePrefix = '';
    }

    public function testGetTablePrefix(): void
    {
        $generator = new ModelGenerator();
        $prefix = $generator->getTablePrefix();
        $this->assertIsString($prefix);
    }

    public function testAutoCompleteData(): void
    {
        $generator = new ModelGenerator();
        $data = $generator->autoCompleteData();
        $this->assertArrayHasKey('tableName', $data);
    }

    public function testGetDbConnection(): void
    {
        $generator = new ModelGenerator();
        $db = $this->invoke($generator, 'getDbConnection');
        $this->assertInstanceOf('yii\db\Connection', $db);
    }

    public function testGetDbDriverName(): void
    {
        $generator = new ModelGenerator();
        $driverName = $this->invoke($generator, 'getDbDriverName');
        $this->assertEquals('sqlite', $driverName);
    }

    public function testGenerateQueryClassName(): void
    {
        $generator = new ModelGenerator();
        $generator->queryClass = 'CustomQuery';
        $result = $this->invoke($generator, 'generateQueryClassName', ['Profile']);
        $this->assertEquals('CustomQuery', $result);
    }

    public function testGenerateQueryClassNameEmpty(): void
    {
        $generator = new ModelGenerator();
        $generator->queryClass = '';
        $generator->tableName = 'profile';
        $result = $this->invoke($generator, 'generateQueryClassName', ['Profile']);
        $this->assertEquals('ProfileQuery', $result);
    }

    public function testGenerateQueryClassNameWithStar(): void
    {
        $generator = new ModelGenerator();
        $generator->queryClass = 'CustomQuery';
        $generator->tableName = '*';
        $result = $this->invoke($generator, 'generateQueryClassName', ['Profile']);
        $this->assertEquals('ProfileQuery', $result);
    }

    public function testGenerateWithQuery(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';
        $generator->generateQuery = true;
        $generator->queryNs = 'app\models';

        $files = $generator->generate();
        $fileNames = array_map(static function ($f) {
            return basename($f->path);
        }, $files);
        $this->assertContains('Profile.php', $fileNames);
        $this->assertContains('ProfileQuery.php', $fileNames);
    }

    public function testGenerateWithoutQuery(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';
        $generator->generateQuery = false;

        $files = $generator->generate();
        $fileNames = array_map(static function ($f) {
            return basename($f->path);
        }, $files);
        $this->assertContains('Profile.php', $fileNames);
        $this->assertNotContains('ProfileQuery.php', $fileNames);
    }

    public function testGenerateLabelsFromComments(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';
        $generator->generateLabelsFromComments = true;

        $files = $generator->generate();
        $this->assertNotEmpty($files);
    }

    public function testGenerateRelationNameFromDestinationTable(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';
        $generator->generateRelationNameFromDestinationTable = true;

        $files = $generator->generate();
        $this->assertNotEmpty($files);
    }

    public function testGenerateWithAllRelationsInverse(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';
        $generator->generateRelations = ModelGenerator::RELATIONS_ALL_INVERSE;

        $files = $generator->generate();
        $this->assertNotEmpty($files);
    }

    public function testGenerateWithNoRelations(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';
        $generator->generateRelations = ModelGenerator::RELATIONS_NONE;

        $files = $generator->generate();
        $this->assertNotEmpty($files);
    }

    public function testActionGenerateClassName(): void
    {
        $generator = new ModelGenerator();
        $generator->tableName = 'profile';
        $result = $generator->actionGenerateClassName();
        $this->assertEquals('Profile', $result);
    }

    public function testIsEnumWithEnumColumn(): void
    {
        $generator = new ModelGenerator();
        $column = new ColumnSchema([
            'name' => 'type',
            'dbType' => "enum('a','b')",
            'enumValues' => ['a', 'b'],
        ]);
        $result = $this->invoke($generator, 'isEnum', [$column]);
        $this->assertTrue($result);
    }

    public function testIsEnumWithNonEnumColumn(): void
    {
        $generator = new ModelGenerator();
        $column = new ColumnSchema([
            'name' => 'id',
            'dbType' => 'integer',
            'enumValues' => null,
        ]);
        $result = $this->invoke($generator, 'isEnum', [$column]);
        $this->assertFalse($result);
    }

    public function testIsColumnAutoIncremental(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';

        $db = Yii::$app->db;
        $table = $db->getTableSchema('profile');

        $result = $this->invoke($generator, 'isColumnAutoIncremental', [$table, ['id']]);
        $this->assertTrue($result);
    }

    public function testRequiredTemplatesWithQueryClass(): void
    {
        $generator = new ModelGenerator();
        $generator->queryClass = 'ProfileQuery';
        $templates = $generator->requiredTemplates();
        $this->assertContains('model.php', $templates);
        $this->assertContains('query.php', $templates);
    }

    public function testRequiredTemplatesWithoutQueryClass(): void
    {
        $generator = new ModelGenerator();
        $generator->queryClass = null;
        $templates = $generator->requiredTemplates();
        $this->assertContains('model.php', $templates);
        $this->assertNotContains('query.php', $templates);
    }

    public function testUseTablePrefix(): void
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'profile';
        $generator->useTablePrefix = true;

        $files = $generator->generate();
        $this->assertNotEmpty($files);
    }
}

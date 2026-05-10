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
use yii\gii\generators\crud\Generator;
use yii\helpers\FileHelper;
use yiiunit\gii\GiiTestCase;

class CrudGeneratorMethodsTest extends GiiTestCase
{
    public function testGenerateActiveFieldForTableColumn(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->controllerClass = 'app\controllers\ProfileController';

        $tableSchema = $generator->getTableSchema();
        $this->assertNotFalse($tableSchema);

        if (isset($tableSchema->columns['description'])) {
            $result = $generator->generateActiveField('description');
            $this->assertStringContainsString("field(\$model, 'description')", $result);
        }
    }

    public function testGenerateActiveSearchFieldWithNoSchema(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yii\base\Model';
        $generator->controllerClass = 'app\controllers\ModelController';

        $result = $generator->generateActiveSearchField('name');
        $this->assertStringContainsString("field(\$model, 'name')", $result);
    }

    public function testGenerateSearchConditionsWithSchema(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->controllerClass = 'app\controllers\ProfileController';

        $conditions = $generator->generateSearchConditions();
        $this->assertNotEmpty($conditions);
    }

    public function testGenerateSearchConditionsWithoutSchema(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yii\base\Model';
        $generator->controllerClass = 'app\controllers\ModelController';

        $conditions = $generator->generateSearchConditions();
        $this->assertIsArray($conditions);
    }

    public function testGetTableSchemaReturnsFalseForNonDbModel(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yii\base\Model';
        $generator->controllerClass = 'app\controllers\ModelController';

        $schema = $generator->getTableSchema();
        $this->assertFalse($schema);
    }

    public function testGetColumnNamesForNonDbModel(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->controllerClass = 'app\controllers\ProfileController';

        $columns = $generator->getColumnNames();
        $this->assertNotEmpty($columns);
    }

    public function testGenerateUrlParamsForNonDbModel(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->controllerClass = 'app\controllers\ProfileController';

        $params = $generator->generateUrlParams();
        $this->assertNotEmpty($params);
    }

    public function testGenerateActionParamsForModel(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->controllerClass = 'app\controllers\ProfileController';

        $params = $generator->generateActionParams();
        $this->assertNotEmpty($params);
    }

    public function testGenerateActionParamCommentsForModel(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->controllerClass = 'app\controllers\ProfileController';

        $comments = $generator->generateActionParamComments();
        $this->assertNotEmpty($comments);
        foreach ($comments as $comment) {
            $this->assertStringContainsString('@param', $comment);
        }
    }

    public function testGetNameAttributeForModel(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->controllerClass = 'app\controllers\ProfileController';

        $nameAttr = $generator->getNameAttribute();
        $this->assertNotEmpty($nameAttr);
    }

    public function testValidateModelClassWithoutPrimaryKey(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yii\base\Model';
        $generator->controllerClass = 'app\controllers\ModelController';

        $generator->validateModelClass();
        $this->assertArrayHasKey('modelClass', $generator->getErrors());
    }

    public function testGenerateWithSearchModel(): void
    {
        FileHelper::createDirectory(Yii::getAlias('@app/controllers'));
        FileHelper::createDirectory(Yii::getAlias('@app/models'));

        $generator = new Generator();
        $generator->template = 'default';
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->controllerClass = 'app\controllers\ProfileController';
        $generator->searchModelClass = 'app\models\ProfileSearch';

        $valid = $generator->validate();
        $this->assertTrue($valid, 'Validation failed: ' . print_r($generator->getErrors(), true));

        $files = $generator->generate();
        $this->assertNotEmpty($files);

        $fileNames = array_map(static function ($f) {
            return basename($f->path);
        }, $files);
        $this->assertContains('ProfileController.php', $fileNames);
        $this->assertContains('ProfileSearch.php', $fileNames);
    }

    public function testGenerateWithoutSearchModel(): void
    {
        FileHelper::createDirectory(Yii::getAlias('@app/controllers'));

        $generator = new Generator();
        $generator->template = 'default';
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->controllerClass = 'app\controllers\ProfileController';
        $generator->searchModelClass = '';

        $files = $generator->generate();
        $this->assertNotEmpty($files);

        $fileNames = array_map(static function ($f) {
            return basename($f->path);
        }, $files);
        $this->assertContains('ProfileController.php', $fileNames);
        $this->assertNotContains('ProfileSearch.php', $fileNames);
    }

    public function testGenerateColumnFormatForAllTypes(): void
    {
        $g = new Generator();

        $c = new ColumnSchema(['phpType' => 'boolean', 'type' => 'boolean', 'name' => 'active']);
        $this->assertEquals('boolean', $g->generateColumnFormat($c));

        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'text', 'name' => 'body']);
        $this->assertEquals('ntext', $g->generateColumnFormat($c));

        $c = new ColumnSchema(['phpType' => 'integer', 'type' => 'integer', 'name' => 'created_time']);
        $this->assertEquals('datetime', $g->generateColumnFormat($c));

        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'string', 'name' => 'user_email']);
        $this->assertEquals('email', $g->generateColumnFormat($c));

        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'string', 'name' => 'page_url']);
        $this->assertEquals('url', $g->generateColumnFormat($c));

        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'string', 'name' => 'title']);
        $this->assertEquals('text', $g->generateColumnFormat($c));
    }

    public function testStrictInflectorControllerID(): void
    {
        $g = new Generator();
        $g->strictInflector = true;
        $g->controllerClass = '\app\controllers\ABCTestController';
        $this->assertEquals('a-b-c-test', $g->getControllerID());

        $g->strictInflector = false;
        $g->controllerClass = '\app\controllers\ABCTestController';
        $this->assertEquals('abctest', $g->getControllerID());
    }

    public function testGetClassDbDriverName(): void
    {
        $generator = new Generator();
        $generator->modelClass = 'yiiunit\gii\Profile';
        $generator->controllerClass = 'app\controllers\ProfileController';

        $driverName = $this->invoke($generator, 'getClassDbDriverName');
        $this->assertEquals('sqlite', $driverName);
    }
}

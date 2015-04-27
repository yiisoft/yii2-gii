<?php
namespace yiiunit\extensions\gii;

use yii\gii\generators\model\Generator as ModelGenerator;
use Yii;
/**
 * SchemaTest checks that Gii model generator supports multiple schemas
 * @group gii
 * @group pgsql
 */
class SchemaTest extends GiiTestCase
{
    protected $driverName = 'pgsql';

    public function testPrefixesGenerator()
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'schema1.*';

        $files = $generator->generate();

        if (version_compare(str_replace('-dev', '', Yii::getVersion()), '2.0.4', '<')) {
            $this->markTestSkipped('This feature is only available since Yii 2.0.4.');
        }

        $this->assertEquals(2, count($files));
        $this->assertEquals("Schema1Table1", basename($files[0]->path, '.php'));
        $this->assertEquals("Schema1Table2", basename($files[1]->path, '.php'));
    }

    public function testRelationsGenerator()
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'schema1.*';

        $files = $generator->generate();
        $this->assertEquals(2, count($files));
        $modelCode = $files[0]->content;
        $modelClass = basename($files[0]->path, '.php');

        if (version_compare(str_replace('-dev', '', Yii::getVersion()), '2.0.4', '<')) {
            $this->markTestSkipped('This feature is only available since Yii 2.0.4.');
        }

        $relations = [
            "\$this->hasMany(Schema2Table1::className(), ['fk1' => 'fk2', 'fk2' => 'fk1']);",
            "\$this->hasMany(Schema2Table1::className(), ['fk3' => 'fk4', 'fk4' => 'fk3']);",
            "\$this->hasOne(Schema2Table2::className(), ['fk1' => 'fk1', 'fk2' => 'fk2']);",
        ];
        foreach ($relations as $relation) {
            $this->assertTrue(strpos($modelCode, $relation) !== false, "Model $modelClass should contain this relation: $relation.\n$modelCode");
        }

        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'schema2.*';

        $files = $generator->generate();
        $this->assertEquals(2, count($files));
        $modelCode = $files[0]->content;
        $modelClass = basename($files[0]->path, '.php');

        $relations = [
            "\$this->hasOne(Schema1Table1::className(), ['fk2' => 'fk1', 'fk1' => 'fk2']);",
            "\$this->hasOne(Schema1Table1::className(), ['fk4' => 'fk3', 'fk3' => 'fk4']);",
            "\$this->hasOne(Schema2Table2::className(), ['fk5' => 'fk5', 'fk6' => 'fk6']);",
        ];
        foreach ($relations as $relation) {
            $this->assertTrue(strpos($modelCode, $relation) !== false, "Model $modelClass should contain this relation: $relation.\n$modelCode");
        }
    }
}

<?php
namespace yiiunit\extensions\gii;

use yii\gii\generators\model\Generator as ModelGenerator;

/**
 * ModelGeneratorTest checks that Gii model generator produces valid results
 * @group gii
 */
class ModelGeneratorTest extends GiiTestCase
{
    public function testAll()
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = '*';

        $valid = $generator->validate();
        $this->assertTrue($valid, 'Validation failed: ' . print_r($generator->getErrors(), true));

        $files = $generator->generate();
        $this->assertEquals(7, count($files));
        $expectedNames = [
            'Category.php',
            'CategoryPhoto.php',
            'Customer.php',
            'Product.php',
            'ProductLanguage.php',
            'Profile.php',
            'Supplier.php',
        ];
        $fileNames = array_map(function ($f) {
            return basename($f->path);
        }, $files);
        sort($fileNames);
        $this->assertEquals($expectedNames, $fileNames);
    }

    /**
     * @return array
     */
    public function relationsProvider()
    {
        return [
            ['category', 'Category.php', [
                [
                    'relation' => "\$this->hasMany(CategoryPhoto::className(), ['category_id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'relation' => "\$this->hasOne(Product::className(), ['category_id' => 'id', 'category_language_code' => 'language_code']);",
                    'expected' => true,
                ],
            ]],
            ['category_photo', 'CategoryPhoto.php', [
                [
                    'relation' => "\$this->hasOne(Category::className(), ['id' => 'category_id']);",
                    'expected' => true,
                ],
            ]],
            ['supplier', 'Supplier.php', [
                [
                    'relation' => "\$this->hasMany(Product::className(), ['supplier_id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'relation' => "\$this->hasOne(ProductLanguage::className(), ['supplier_id' => 'id']);",
                    'expected' => true,
                ],
            ]],
            ['product', 'Product.php', [
                [
                    'relation' => "\$this->hasOne(Supplier::className(), ['id' => 'supplier_id']);",
                    'expected' => true,
                ],
                [
                    'relation' => "\$this->hasOne(Category::className(), ['id' => 'category_id', 'language_code' => 'category_language_code']);",
                    'expected' => true,
                ],
                [
                    'relation' => "\$this->hasOne(ProductLanguage::className(), ['supplier_id' => 'supplier_id', 'id' => 'id']);",
                    'expected' => true,
                ],
            ]],
            ['product_language', 'ProductLanguage.php', [
                [
                    'relation' => "\$this->hasOne(Product::className(), ['supplier_id' => 'supplier_id', 'id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'relation' => "\$this->hasOne(Supplier::className(), ['id' => 'supplier_id']);",
                    'expected' => true,
                ],
            ]],
        ];
    }

    /**
     * @dataProvider relationsProvider
     * @param $tableName string
     * @param $fileName string
     * @param $relations array
     */
    public function testRelations($tableName, $fileName, $relations)
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = $tableName;

        $files = $generator->generate();
        $this->assertEquals(1, count($files));
        $this->assertEquals($fileName, basename($files[0]->path));

        $code = $files[0]->content;
        foreach ($relations as $relation) {
            $location = strpos($code, $relation['relation']);
            $this->assertTrue(
                $relation['expected'] ? $location !== false : $location === false,
                "Relation \"{$relation['relation']}\" should"
                . ($relation['expected'] ? '' : ' not')." be there:\n" . $code
            );
        }
    }

    public function testSchemas()
    {

    }

    /**
     * @return array
     */
    public function rulesProvider()
    {
        return [
            ['category_photo', 'CategoryPhoto.php', [
                "[['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],",
                "[['category_id', 'display_number'], 'unique', 'targetAttribute' => ['category_id', 'display_number'], 'message' => 'The combination of Category ID and Display Number has already been taken.'],",
            ]],
            ['product', 'Product.php', [
                "[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],",
                "[['category_id', 'category_language_code'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id', 'category_language_code' => 'language_code']],",
                "[['category_id', 'category_language_code'], 'unique', 'targetAttribute' => ['category_id', 'category_language_code'], 'message' => 'The combination of Category Language Code and Category ID has already been taken.'],"
            ]],
            ['product_language', 'ProductLanguage.php', [
                "[['supplier_id', 'id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['supplier_id' => 'supplier_id', 'id' => 'id']],",
                "[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],",
                "[['supplier_id'], 'unique']",
                "[['id', 'supplier_id', 'language_code'], 'unique', 'targetAttribute' => ['id', 'supplier_id', 'language_code'], 'message' => 'The combination of ID, Supplier ID and Language Code has already been taken.']",
                "[['id', 'supplier_id'], 'unique', 'targetAttribute' => ['id', 'supplier_id'], 'message' => 'The combination of ID and Supplier ID has already been taken.']",
            ]],
        ];
    }

    /**
     * @dataProvider rulesProvider
     *
     * @param $tableName string
     * @param $fileName string
     * @param $rules array
     */
    public function testRules($tableName, $fileName, $rules)
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = $tableName;

        $files = $generator->generate();
        $this->assertEquals(1, count($files));
        $this->assertEquals($fileName, basename($files[0]->path));

        $code = $files[0]->content;
        foreach ($rules as $rule) {
            $location = strpos($code, $rule);
            $this->assertTrue($location !== false,
                "Rule \"{$rule}\" should be there:\n" . $code
            );
        }
    }
}

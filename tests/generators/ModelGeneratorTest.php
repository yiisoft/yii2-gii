<?php
namespace yiiunit\extensions\gii\generators;

use yii\gii\generators\model\Generator as ModelGenerator;
use yiiunit\extensions\gii\GiiTestCase;

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

        $generator->queryNs = 'application\models';

        $valid = $generator->validate();
        $this->assertFalse($valid);
        $this->assertEquals($generator->getFirstError('queryNs'), 'Namespace must be associated with an existing directory.');

        $generator->queryNs = 'app\models';

        $valid = $generator->validate();
        $this->assertTrue($valid, 'Validation failed: ' . print_r($generator->getErrors(), true));

        $files = $generator->generate();
        $this->assertEquals(8, count($files));
        $expectedNames = [
            'Attribute.php',
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
                    'name' => 'function getCategoryPhotos()',
                    'relation' => "\$this->hasMany(CategoryPhoto::class, ['category_id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getProduct()',
                    'relation' => "\$this->hasOne(Product::class, ['category_id' => 'id', 'category_language_code' => 'language_code']);",
                    'expected' => true,
                ],
            ]],
            ['category_photo', 'CategoryPhoto.php', [
                [
                    'name' => 'function getCategory()',
                    'relation' => "\$this->hasOne(Category::class, ['id' => 'category_id']);",
                    'expected' => true,
                ],
            ]],
            ['supplier', 'Supplier.php', [
                [
                    'name' => 'function getProducts()',
                    'relation' => "\$this->hasMany(Product::class, ['supplier_id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getAttributes0()',
                    'relation' => "\$this->hasMany(Attribute::class, ['supplier_id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getAttributes()',
                    'relation' => "\$this->hasOne(Attribute::class, ['supplier_id' => 'id']);",
                    'expected' => false,
                ],
                [
                    'name' => 'function getProductLanguage()',
                    'relation' => "\$this->hasOne(ProductLanguage::class, ['supplier_id' => 'id']);",
                    'expected' => true,
                ],
            ]],
            ['product', 'Product.php', [
                [
                    'name' => 'function getSupplier()',
                    'relation' => "\$this->hasOne(Supplier::class, ['id' => 'supplier_id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getCategory()',
                    'relation' => "\$this->hasOne(Category::class, ['id' => 'category_id', 'language_code' => 'category_language_code']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getProductLanguage()',
                    'relation' => "\$this->hasOne(ProductLanguage::class, ['supplier_id' => 'supplier_id', 'id' => 'id']);",
                    'expected' => true,
                ],
            ]],
            ['product_language', 'ProductLanguage.php', [
                [
                    'name' => 'function getSupplier()',
                    'relation' => "\$this->hasOne(Product::class, ['supplier_id' => 'supplier_id', 'id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getSupplier0()',
                    'relation' => "\$this->hasOne(Supplier::class, ['id' => 'supplier_id']);",
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
        $generator->generateRelationsFromCurrentSchema = false;
        $generator->tableName = $tableName;

        $files = $generator->generate();
        $this->assertEquals(1, count($files));
        $this->assertEquals($fileName, basename($files[0]->path));

        $code = $files[0]->content;
        foreach ($relations as $relation) {
            $found = strpos($code, $relation['relation']) !== false;
            $this->assertTrue(
                $relation['expected'] === $found,
                "Relation \"{$relation['relation']}\" should"
                . ($relation['expected'] ? '' : ' not')." be there:\n" . $code
            );

            $found = strpos($code, $relation['name']) !== false;
            $this->assertTrue(
                $relation['expected'] === $found,
                "Relation Name \"{$relation['name']}\" should"
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
                "[['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],",
                "[['category_id', 'display_number'], 'unique', 'targetAttribute' => ['category_id', 'display_number']],",
            ]],
            ['product', 'Product.php', [
                "[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::class, 'targetAttribute' => ['supplier_id' => 'id']],",
                "[['category_id', 'category_language_code'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id', 'category_language_code' => 'language_code']],",
                "[['category_id', 'category_language_code'], 'unique', 'targetAttribute' => ['category_id', 'category_language_code']],"
            ]],
            ['product_language', 'ProductLanguage.php', [
                "[['supplier_id', 'id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['supplier_id' => 'supplier_id', 'id' => 'id']],",
                "[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::class, 'targetAttribute' => ['supplier_id' => 'id']],",
                "[['supplier_id'], 'unique']",
                "[['id', 'supplier_id', 'language_code'], 'unique', 'targetAttribute' => ['id', 'supplier_id', 'language_code']]",
                "[['id', 'supplier_id'], 'unique', 'targetAttribute' => ['id', 'supplier_id']]",
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

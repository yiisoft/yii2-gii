<?php
namespace yiiunit\gii\generators;

use yii\gii\generators\model\Generator as ModelGenerator;
use yiiunit\gii\GiiTestCase;

/**
 * ModelGeneratorTest checks that Gii model generator produces valid results
 * @group gii
 */
class ModelGeneratorTest extends GiiTestCase
{
    public function testDefaultuseClassConstant()
    {
        $generator = new ModelGenerator();
        $this->assertEquals(
            PHP_MAJOR_VERSION > 5  || (PHP_MAJOR_VERSION === 5 && PHP_MINOR_VERSION > 4),
            $generator->useClassConstant
        );

        $generator = new ModelGenerator([
            'useClassConstant' => false,
        ]);
        $this->assertFalse($generator->useClassConstant);

        $generator = new ModelGenerator([
            'useClassConstant' => true,
        ]);
        $this->assertTrue($generator->useClassConstant);
    }

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
        $this->assertEquals(12, count($files));
        $expectedNames = [
            'Attribute.php',
            'BlogRtl.php',
            'Category.php',
            'CategoryPhoto.php',
            'Customer.php',
            'IdentityProvider.php',
            'Organization.php',
            'Product.php',
            'ProductLanguage.php',
            'Profile.php',
            'Supplier.php',
            'UserRtl.php',
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
            ['category', 'Category.php', false, [
                [
                    'name' => 'function getCategoryPhotos()',
                    'relation' => "\$this->hasMany(CategoryPhoto::className(), ['category_id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getProduct()',
                    'relation' => "\$this->hasOne(Product::className(), ['category_id' => 'id', 'category_language_code' => 'language_code']);",
                    'expected' => true,
                ],
            ]],
            ['category_photo', 'CategoryPhoto.php', false, [
                [
                    'name' => 'function getCategory()',
                    'relation' => "\$this->hasOne(Category::className(), ['id' => 'category_id']);",
                    'expected' => true,
                ],
            ]],
            ['supplier', 'Supplier.php', false, [
                [
                    'name' => 'function getProducts()',
                    'relation' => "\$this->hasMany(Product::className(), ['supplier_id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getAttributes0()',
                    'relation' => "\$this->hasMany(Attribute::className(), ['supplier_id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getAttributes()',
                    'relation' => "\$this->hasOne(Attribute::className(), ['supplier_id' => 'id']);",
                    'expected' => false,
                ],
                [
                    'name' => 'function getProductLanguage()',
                    'relation' => "\$this->hasOne(ProductLanguage::className(), ['supplier_id' => 'id']);",
                    'expected' => true,
                ],
            ]],
            ['product', 'Product.php', false, [
                [
                    'name' => 'function getSupplier()',
                    'relation' => "\$this->hasOne(Supplier::className(), ['id' => 'supplier_id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getCategory()',
                    'relation' => "\$this->hasOne(Category::className(), ['id' => 'category_id', 'language_code' => 'category_language_code']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getProductLanguage()',
                    'relation' => "\$this->hasOne(ProductLanguage::className(), ['supplier_id' => 'supplier_id', 'id' => 'id']);",
                    'expected' => true,
                ],
            ]],
            ['product_language', 'ProductLanguage.php', false, [
                [
                    'name' => 'function getSupplier()',
                    'relation' => "\$this->hasOne(Product::className(), ['supplier_id' => 'supplier_id', 'id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getSupplier0()',
                    'relation' => "\$this->hasOne(Supplier::className(), ['id' => 'supplier_id']);",
                    'expected' => true,
                ],
            ]],

            ['organization', 'Organization.php', false, [
                [
                    'name' => 'function getIdentityProviders()',
                    'relation' => "\$this->hasMany(IdentityProvider::className(), ['organization_id' => 'id']);",
                    'expected' => true,
                ],
            ]],
            ['identity_provider', 'IdentityProvider.php', false, [
                [
                    'name' => 'function getOrganization()',
                    'relation' => "\$this->hasOne(Organization::className(), ['id' => 'organization_id']);",
                    'expected' => true,
                ],
            ]],
            ['user_rtl', 'UserRtl.php', false, [
                [
                    'name' => 'function getBlogRtls()',
                    'relation' => "\$this->hasMany(BlogRtl::className(), ['id_user' => 'id']);",
                    'expected' => true,
                ],
            ]],
            ['blog_rtl', 'BlogRtl.php', false, [
                [
                    'name' => 'function getUser()',
                    'relation' => "\$this->hasOne(UserRtl::className(), ['id' => 'id_user']);",
                    'expected' => true,
                ],
            ]],

            // useClassConstant = true
            ['category', 'Category.php', true, [
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
        ];
    }

    /**
     * @dataProvider relationsProvider
     * @param $tableName string
     * @param $fileName string
     * @param $useClassConstant bool
     * @param $relations array
     */
    public function testRelations($tableName, $fileName, $useClassConstant, $relations)
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->generateRelationsFromCurrentSchema = false;
        $generator->useClassConstant = $useClassConstant;
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

    /**
     * @return array
     */
    public function rulesProvider()
    {
        return [
            ['category_photo', 'CategoryPhoto.php', false, [
                "[['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],",
                "[['category_id', 'display_number'], 'unique', 'targetAttribute' => ['category_id', 'display_number']],",
            ]],
            ['product', 'Product.php', false, [
                "[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],",
                "[['category_id', 'category_language_code'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id', 'category_language_code' => 'language_code']],",
                "[['category_id', 'category_language_code'], 'unique', 'targetAttribute' => ['category_id', 'category_language_code']],"
            ]],
            ['product_language', 'ProductLanguage.php', false, [
                "[['supplier_id', 'id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['supplier_id' => 'supplier_id', 'id' => 'id']],",
                "[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],",
                "[['supplier_id'], 'unique']",
                "[['id', 'supplier_id', 'language_code'], 'unique', 'targetAttribute' => ['id', 'supplier_id', 'language_code']]",
                "[['id', 'supplier_id'], 'unique', 'targetAttribute' => ['id', 'supplier_id']]",
            ]],

            // useClassConstant = true
            ['product_language', 'ProductLanguage.php', true, [
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
     * @param $useClassConstant bool
     * @param $rules array
     */
    public function testRules($tableName, $fileName, $useClassConstant, $rules)
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = $tableName;
        $generator->useClassConstant = $useClassConstant;

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

    public function testGenerateStandardizedCapitalsForClassNames()
    {
        $modelGenerator = new ModelGeneratorMock;
        $modelGenerator->standardizeCapitals = true;

        $tableNames = [
            'lower_underline_name' => 'LowerUnderlineName',
            'Ucwords_Underline_Name' => 'UcwordsUnderlineName',
            'UPPER_UNDERLINE_NAME' => 'UpperUnderlineName',
            'lower-hyphen-name' => 'LowerHyphenName',
            'Ucwords-Hyphen-Name' => 'UcwordsHyphenName',
            'UPPER-HYPHEN-NAME' => 'UpperHyphenName',
            'CamelCaseName' => 'CamelCaseName',
            'lowerUcwordsName' => 'LowerUcwordsName',
            'lowername' => 'Lowername',
            'UPPERNAME' => 'Uppername',
        ];

        foreach ($tableNames as $tableName => $expectedClassName) {
            $generatedClassName = $modelGenerator->publicGenerateClassName($tableName);
            $this->assertEquals($expectedClassName, $generatedClassName);
        }
    }

    public function testGenerateNotStandardizedCapitalsForClassNames()
    {
        $modelGenerator = new ModelGeneratorMock;
        $modelGenerator->standardizeCapitals = false;

        $tableNames = [
            'lower_underline_name' => 'LowerUnderlineName',
            'Ucwords_Underline_Name' => 'UcwordsUnderlineName',
            'UPPER_UNDERLINE_NAME' => 'UPPERUNDERLINENAME',
            'ABBRMyTable' => 'ABBRMyTable',
            'lower-hyphen-name' => 'Lower-hyphen-name',
            'Ucwords-Hyphen-Name' => 'Ucwords-Hyphen-Name',
            'UPPER-HYPHEN-NAME' => 'UPPER-HYPHEN-NAME',
            'CamelCaseName' => 'CamelCaseName',
            'lowerUcwordsName' => 'LowerUcwordsName',
            'lowername' => 'Lowername',
            'UPPERNAME' => 'UPPERNAME',
            'PARTIALUpperName' => 'PARTIALUpperName',
        ];

        foreach ($tableNames as $tableName => $expectedClassName) {
            $generatedClassName = $modelGenerator->publicGenerateClassName($tableName);
            $this->assertEquals($expectedClassName, $generatedClassName);
        }
    }

    public function testGenerateSingularizedClassNames()
    {
        $modelGenerator = new ModelGeneratorMock;
        $modelGenerator->singularize = true;

        $tableNames = [
            'clients' => 'Client',
            'client_programs' => 'ClientProgram',
            'noneexistingwords' => 'Noneexistingword',
            'noneexistingword' => 'Noneexistingword',
            'children' => 'Child',
            'good_children' => 'GoodChild',
            'user' => 'User',
        ];

        foreach ($tableNames as $tableName => $expectedClassName) {
            $generatedClassName = $modelGenerator->publicGenerateClassName($tableName);
            $this->assertEquals($expectedClassName, $generatedClassName);
        }
    }

    public function testGenerateNotSingularizedClassNames()
    {
        $modelGenerator = new ModelGeneratorMock;

        $tableNames = [
            'clients' => 'Clients',
            'client_programs' => 'ClientPrograms',
            'noneexistingwords' => 'Noneexistingwords',
            'noneexistingword' => 'Noneexistingword',
            'children' => 'Children',
            'good_children' => 'GoodChildren',
            'user' => 'User',
        ];

        foreach ($tableNames as $tableName => $expectedClassName) {
            $generatedClassName = $modelGenerator->publicGenerateClassName($tableName);
            $this->assertEquals($expectedClassName, $generatedClassName);
        }
    }

    /**
     * @return array
     */
    public function tablePropertiesProvider()
    {
        return [
            [
                'tableName' => 'category_photo',
                'columns' => [
                    [
                        'columnName' => 'id',
                        'propertyRow' => '* @property int $id',
                    ],
                    [
                        'columnName' => 'category_id',
                        'propertyRow' => '* @property int $category_id',
                    ],
                    [
                        'columnName' => 'display_number',
                        'propertyRow' => '* @property int $display_number',
                    ],

                ]
            ],
            [
                'tableName' => 'product',
                'columns' => [
                    [
                        'columnName' => 'id',
                        'propertyRow' => '* @property int $id',
                    ],
                    [
                        'columnName' => 'category_id',
                        'propertyRow' => '* @property int $supplier_id',
                    ],
                    [
                        'columnName' => 'category_language_code',
                        'propertyRow' => '* @property string $category_language_code',
                    ],
                    [
                        'columnName' => 'category_id',
                        'propertyRow' => '* @property int $category_id',
                    ],
                    [
                        'columnName' => 'internal_name',
                        'propertyRow' => '* @property string|null $internal_name',
                    ],

                ]
            ],
        ];

    }

    /**
     * @dataProvider tablePropertiesProvider
     *
     * @param string $tableName
     * @param array $columns
     */
    public function testGenerateProperties($tableName, $columns)
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = $tableName;

        $files = $generator->generate();

        $code = $files[0]->content;
        foreach ($columns as $column) {
            $location = strpos($code, $column['propertyRow']);
            $this->assertTrue(
                $location !== false,
                "Column \"{$column['columnName']}\" properties should be there:\n" . $column['propertyRow']
            );
        }

    }
}

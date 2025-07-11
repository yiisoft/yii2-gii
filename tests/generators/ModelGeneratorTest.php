<?php
namespace yiiunit\gii\generators;

use yii\db\mysql\ColumnSchema;
use yii\db\TableSchema;
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
            ['product_language', 'ProductLanguage.php', false, [
                [
                    'name' => 'function getProduct()',
                    'relation' => "\$this->hasOne(Product::className(), ['supplier_id' => 'supplier_id', 'id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getSupplier()',
                    'relation' => "\$this->hasOne(Supplier::className(), ['id' => 'supplier_id']);",
                    'expected' => true,
                ],
            ],
             true // $fromDestTable
            ],

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
            ['blog_rtl', 'BlogRtl.php', false, [
                [
                    'name' => 'function getUserRtl()',
                    'relation' => "\$this->hasOne(UserRtl::className(), ['id' => 'id_user']);",
                    'expected' => true,
                ],
            ],
             true
            ],

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
     * @param $fromDestTable bool
     */
    public function testRelations($tableName, $fileName, $useClassConstant, $relations, $fromDestTable = false)
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->generateRelationsFromCurrentSchema = false;
        $generator->generateRelationNameFromDestinationTable = $fromDestTable;
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

    public function testEnum()
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'category_photo';

        $tableSchema = $this->createEnumTableSchema();
        $params = [
            'tableName' => $tableSchema->name,
            'className' => 'TestEnumModel',
            'queryClassName' => false,
            'tableSchema' => $tableSchema,
            'properties' => [],
            'labels' => $generator->generateLabels($tableSchema),
            'rules' => $generator->generateRules($tableSchema),
            'relations' => [],
            'relationsClassHints' => [],
            'enum' => $generator->getEnum($tableSchema->columns),
        ];
        $codeFile = $generator->render('model.php', $params);

        /**
         * Fix class code for eval - remove ?php, namespace and use Yii
         */
        $classCode = str_replace('<?php', '', $codeFile);
        $classCode = str_replace('namespace app\models;', '', $classCode);
        $classCode = str_replace('use Yii;', '', $classCode);

        /**
         * Add method getTableSchema for setting test schema
         */
        $classCode = substr($classCode, 0, strrpos($classCode, "\n"));
        $classCode = substr($classCode, 0, strrpos($classCode, "\n"));
        $classCode .= '
    public static $testTableSchema;
    public static function getTableSchema(){
        return self::$testTableSchema;
    }
}
        ';
        if (!class_exists('TestEnumModel')) {
            eval($classCode);
        }

        $testEnumModel = new \TestEnumModel();
        $testEnumModel::$testTableSchema = $this->createEnumTableSchema();

        foreach(
            [
                [
                    'value'=>'Client',
                    'constant'=>'TYPE_CLIENT',
                    'set'=>'setTypeToClient',
                    'isSet'=>'isTypeClient',
                ],
                [
                    'value' => 'Consignees',
                    'constant'=>'TYPE_CONSIGNEES',
                    'set' => 'setTypeToConsignees',
                    'isSet' => 'isTypeConsignees',
                ],
                [
                    'value' => 'Car cleaner',
                    'constant'=>'TYPE_CAR_CLEANER',
                    'set' => 'setTypeToCarCleaner',
                    'isSet' => 'isTypeCarCleaner',
                ],
                [
                    'value' => 'B+',
                    'constant'=>'TYPE_B_PLUS',
                    'set' => 'setTypeToBPlus',
                    'isSet' => 'isTypeBPlus',
                ],
                [
                    'value' => 'B-',
                    'constant'=>'TYPE_B_MINUS',
                    'set' => 'setTypeToBMinus',
                    'isSet' => 'isTypeBMinus',
                ],
                [
                    'value' => 'A-Foo',
                    'constant'=>'TYPE_A_FOO',
                    'set' => 'setTypeToAFoo',
                    'isSet' => 'isTypeAFoo',
                ],
                [
                    'value' => '-A',
                    'constant'=>'TYPE_MINUS_A',
                    'set' => 'setTypeToMinusA',
                    'isSet' => 'isTypeMinusB',
                ]
            ] as $tesEnum
        ) {
            $this->assertTrue(
                defined('\TestEnumModel::'.$tesEnum['constant']),
                'Constant ' . $tesEnum['constant'] . ' should be defined. ' . $classCode
            );
            $this->assertTrue(
                method_exists($testEnumModel, $tesEnum['set']),
                'Moethod  ' . $tesEnum['set'] . ' not exist. ' . $classCode
            );
            $this->assertTrue(
                method_exists($testEnumModel,$tesEnum['isSet']),
                'Moethod  ' . $tesEnum['isSet'] . ' not exist. ' . $classCode
            );
            $testEnumModel->type = constant('\TestEnumModel::'.$tesEnum['constant']);
            $this->assertTrue($testEnumModel->validate());
            $testEnumModel->{$tesEnum['set']}();
            $this->assertTrue($testEnumModel->{$tesEnum['isSet']}());

            $opts = $testEnumModel::optsType();
            $this->assertArrayHasKey($tesEnum['value'], $opts, 'Enum value ' . $tesEnum['value'] . ' should be in optsType. ' . print_r($opts, true));

        }

        /** test validate */
        $this->assertTrue($testEnumModel->validate());
        $testEnumModel->type = '11111';
        $this->assertFalse($testEnumModel->validate());

    }

    public function testEnumDuplicateEnumNames()
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'category_photo';
        $tableSchema = $this->createEnumTableSchemaDuplicateEnumConstantName();
        $generator->generateRules($tableSchema);
        $generatorErrors = $generator->errors;
        $this->assertArrayHasKey('tableName', $generatorErrors, 'Enum column \'type\' has generated duplicate constant names. Error: ' . print_r($generatorErrors, true));
        $this->assertStringStartsWith("Enum column 'type' has generated duplicate constant names ", $generatorErrors['tableName'][0]);
    }

    public function createEnumTableSchema()
    {
        $schema = new TableSchema();
        $schema->name = 'company_type';
        $schema->fullName = 'company_type';
        $schema->primaryKey = ['id'];
        $schema->columns = [
            'id' => new ColumnSchema([
                'name' => 'id',
                'allowNull' => false,
                'type' => 'smallint',
                'phpType' => 'integer',
                'dbType' => 'smallint(5) unsigned',
                'size' => 5,
                'precision' => 5,
                'isPrimaryKey' => true,
                'autoIncrement' => true,
                'unsigned' => true,
                'comment' => ''
            ]),
            'type' => new ColumnSchema([
                'name' => 'type',
                'allowNull' => true,
                'type' => 'string',
                'phpType' => 'string',
                'dbType' => 'enum(\'Client\',\'Consignees\',\'Car cleaner\',\'B+\',\'B-\',\'A-Foo\',\'-A\')',
                'enumValues' => [
                    0 => 'Client',
                    1 => 'Consignees',
                    2 => 'Car cleaner',
                    3 => 'B+',
                    4 => 'B-',
                    5 => 'A-Foo',
                    6 => '-A',
                ],
                'size' => null,
                'precision' => null,
                'isPrimaryKey' => false,
                'autoIncrement' => false,
                'unsigned' => false,
                'comment' => ''
            ]),
        ];

        return $schema;
    }

    public function createEnumTableSchemaDuplicateEnumConstantName()
    {
        $schema = new TableSchema();
        $schema->name = 'company_type';
        $schema->fullName = 'company_type';
        $schema->primaryKey = ['id'];
        $schema->columns = [
            'id' => new ColumnSchema([
                'name' => 'id',
                'allowNull' => false,
                'type' => 'smallint',
                'phpType' => 'integer',
                'dbType' => 'smallint(5) unsigned',
                'size' => 5,
                'precision' => 5,
                'isPrimaryKey' => true,
                'autoIncrement' => true,
                'unsigned' => true,
                'comment' => ''
            ]),
            'type' => new ColumnSchema([
                'name' => 'type',
                'allowNull' => true,
                'type' => 'string',
                'phpType' => 'string',
                'dbType' => 'enum(\'B -\',\'B-\')',
                'enumValues' => [
                    0 => 'B -',
                    1 => 'B-'
                ],
                'size' => null,
                'precision' => null,
                'isPrimaryKey' => false,
                'autoIncrement' => false,
                'unsigned' => false,
                'comment' => ''
            ]),
        ];

        return $schema;
    }
}

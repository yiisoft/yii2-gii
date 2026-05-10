<?php

declare(strict_types=1);

namespace yiiunit\gii;

use Yii;
use yii\base\InvalidConfigException;
use yii\gii\Generator;
use yiiunit\gii\generators\ConcreteGenerator;

class GeneratorBaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    public function testInitSetsDefaultTemplate(): void
    {
        $generator = new ConcreteGenerator();
        $this->assertArrayHasKey('default', $generator->templates);
    }

    public function testAttributeLabels(): void
    {
        $generator = new ConcreteGenerator();
        $labels = $generator->attributeLabels();
        $this->assertArrayHasKey('template', $labels);
        $this->assertArrayHasKey('enableI18N', $labels);
        $this->assertArrayHasKey('messageCategory', $labels);
    }

    public function testRequiredTemplatesReturnsEmptyArray(): void
    {
        $generator = new ConcreteGenerator();
        $this->assertEquals([], $generator->requiredTemplates());
    }

    public function testStickyAttributes(): void
    {
        $generator = new ConcreteGenerator();
        $sticky = $generator->stickyAttributes();
        $this->assertContains('template', $sticky);
        $this->assertContains('enableI18N', $sticky);
        $this->assertContains('messageCategory', $sticky);
    }

    public function testHints(): void
    {
        $generator = new ConcreteGenerator();
        $hints = $generator->hints();
        $this->assertArrayHasKey('enableI18N', $hints);
        $this->assertArrayHasKey('messageCategory', $hints);
    }

    public function testAutoCompleteDataReturnsEmptyArray(): void
    {
        $generator = new ConcreteGenerator();
        $this->assertEquals([], $generator->autoCompleteData());
    }

    public function testSuccessMessage(): void
    {
        $generator = new ConcreteGenerator();
        $this->assertEquals('The code has been generated successfully.', $generator->successMessage());
    }

    public function testFormView(): void
    {
        $generator = new ConcreteGenerator();
        $this->assertStringEndsWith('/form.php', $generator->formView());
    }

    public function testDefaultTemplate(): void
    {
        $generator = new ConcreteGenerator();
        $this->assertStringEndsWith('/default', $generator->defaultTemplate());
    }

    public function testGetDescriptionReturnsEmptyString(): void
    {
        $generator = new ConcreteGenerator();
        $this->assertEquals('', $generator->getDescription());
    }

    public function testRules(): void
    {
        $generator = new ConcreteGenerator();
        $rules = $generator->rules();
        $this->assertNotEmpty($rules);
    }

    public function testValidateTemplateWithInvalidTemplate(): void
    {
        $generator = new ConcreteGenerator();
        $generator->template = 'nonexistent';
        $generator->validateTemplate();
        $this->assertArrayHasKey('template', $generator->getErrors());
    }

    public function testValidateTemplateWithValidTemplate(): void
    {
        $generator = new ConcreteGenerator();
        $generator->template = 'default';
        $generator->validateTemplate();
        $this->assertArrayNotHasKey('template', $generator->getErrors());
    }

    public function testValidateClassWithExistingClass(): void
    {
        $generator = new ConcreteGenerator();
        $generator->testClass = Generator::class;
        $generator->validateClass('testClass', []);
        $this->assertArrayNotHasKey('testClass', $generator->getErrors());
    }

    public function testValidateClassWithNonExistentClass(): void
    {
        $generator = new ConcreteGenerator();
        $generator->testClass = 'NonExistentClass';
        $generator->validateClass('testClass', []);
        $this->assertArrayHasKey('testClass', $generator->getErrors());
    }

    public function testValidateClassWithExtendsOption(): void
    {
        $generator = new ConcreteGenerator();
        $generator->testClass = Generator::class;
        $generator->validateClass('testClass', ['extends' => Generator::class]);
        $this->assertArrayNotHasKey('testClass', $generator->getErrors());
    }

    public function testValidateClassWithInvalidExtends(): void
    {
        $generator = new ConcreteGenerator();
        $generator->testClass = Generator::class;
        $generator->validateClass('testClass', ['extends' => 'yii\db\Connection']);
        $this->assertArrayHasKey('testClass', $generator->getErrors());
    }

    public function testValidateNewClassWithNoNamespace(): void
    {
        $generator = new ConcreteGenerator();
        $generator->testClass = 'NoNamespaceClass';
        $generator->validateNewClass('testClass', []);
        $this->assertArrayHasKey('testClass', $generator->getErrors());
    }

    public function testValidateNewClassWithInvalidNamespace(): void
    {
        $generator = new ConcreteGenerator();
        $generator->testClass = '\NonExistentNamespace\SomeClass';
        $generator->validateNewClass('testClass', []);
        $this->assertArrayHasKey('testClass', $generator->getErrors());
    }

    public function testValidateNewClassWithValidNamespace(): void
    {
        $generator = new ConcreteGenerator();
        $generator->testClass = '\yii\gii\SomeNewClass';
        $generator->validateNewClass('testClass', []);
        $this->assertArrayNotHasKey('testClass', $generator->getErrors());
    }

    public function testValidateMessageCategoryWhenI18nDisabled(): void
    {
        $generator = new ConcreteGenerator();
        $generator->enableI18N = false;
        $generator->messageCategory = '';
        $generator->validateMessageCategory();
        $this->assertArrayNotHasKey('messageCategory', $generator->getErrors());
    }

    public function testValidateMessageCategoryWhenI18nEnabledWithEmptyCategory(): void
    {
        $generator = new ConcreteGenerator();
        $generator->enableI18N = true;
        $generator->messageCategory = '';
        $generator->validateMessageCategory();
        $this->assertArrayHasKey('messageCategory', $generator->getErrors());
    }

    public function testValidateMessageCategoryWhenI18nEnabledWithInvalidCategory(): void
    {
        $generator = new ConcreteGenerator();
        $generator->enableI18N = true;
        $generator->messageCategory = 'invalid category!';
        $generator->validateMessageCategory();
        $this->assertArrayHasKey('messageCategory', $generator->getErrors());
    }

    public function testValidateMessageCategoryWhenI18nEnabledWithValidCategory(): void
    {
        $generator = new ConcreteGenerator();
        $generator->enableI18N = true;
        $generator->messageCategory = 'app';
        $generator->validateMessageCategory();
        $this->assertArrayNotHasKey('messageCategory', $generator->getErrors());
    }

    public function testIsReservedKeyword(): void
    {
        $generator = new ConcreteGenerator();
        $this->assertTrue($generator->isReservedKeyword('class'));
        $this->assertTrue($generator->isReservedKeyword('function'));
        $this->assertTrue($generator->isReservedKeyword('return'));
        $this->assertTrue($generator->isReservedKeyword('Class'));
        $this->assertTrue($generator->isReservedKeyword('IF'));
        $this->assertFalse($generator->isReservedKeyword('model'));
        $this->assertFalse($generator->isReservedKeyword('User'));
        $this->assertFalse($generator->isReservedKeyword('myVar'));
    }

    public function testGenerateStringWithoutI18n(): void
    {
        $generator = new ConcreteGenerator();
        $generator->enableI18N = false;
        $this->assertEquals("'hello world'", $generator->generateString('hello world'));
    }

    public function testGenerateStringWithoutI18nWithPlaceholders(): void
    {
        $generator = new ConcreteGenerator();
        $generator->enableI18N = false;
        $result = $generator->generateString('Hello {name}', ['name' => 'John']);
        $this->assertEquals("'Hello John'", $result);
    }

    public function testGenerateStringWithI18n(): void
    {
        $generator = new ConcreteGenerator();
        $generator->enableI18N = true;
        $generator->messageCategory = 'app';
        $result = $generator->generateString('hello world');
        $this->assertEquals("Yii::t('app', 'hello world')", $result);
    }

    public function testGenerateStringWithI18nWithPlaceholders(): void
    {
        $generator = new ConcreteGenerator();
        $generator->enableI18N = true;
        $generator->messageCategory = 'app';
        $result = $generator->generateString('Hello {name}', ['name' => 'John']);
        $this->assertStringContainsString("Yii::t('app', 'Hello {name}'", $result);
        $this->assertStringContainsString("'name' => 'John'", $result);
    }

    public function testGetTemplatePathWithValidTemplate(): void
    {
        $generator = new ConcreteGenerator();
        $path = $generator->getTemplatePath();
        $this->assertDirectoryExists($path);
    }

    public function testGetTemplatePathWithInvalidTemplate(): void
    {
        $generator = new ConcreteGenerator();
        $generator->template = 'nonexistent';
        $this->expectException(InvalidConfigException::class);
        $generator->getTemplatePath();
    }

    public function testGetStickyDataFile(): void
    {
        $generator = new ConcreteGenerator();
        $path = $generator->getStickyDataFile();
        $this->assertStringContainsString('gii-', $path);
        $this->assertStringContainsString('.json', $path);
    }

    public function testSaveAndLoadStickyAttributes(): void
    {
        $generator = new ConcreteGenerator();
        $generator->enableI18N = true;
        $generator->messageCategory = 'test-category';
        $generator->saveStickyAttributes();

        $generator2 = new ConcreteGenerator();
        $generator2->enableI18N = false;
        $generator2->messageCategory = 'other';
        $generator2->loadStickyAttributes();

        $this->assertTrue($generator2->enableI18N);
        $this->assertEquals('test-category', $generator2->messageCategory);

        $stickyFile = $generator->getStickyDataFile();
        if (file_exists($stickyFile)) {
            @unlink($stickyFile);
            @rmdir(dirname($stickyFile));
        }
    }

    public function testSaveCodeFiles(): void
    {
        $dir = Yii::getAlias('@app/runtime/gii_save_test_' . uniqid());
        @mkdir($dir, 0777, true);

        $generator = new ConcreteGenerator();
        $filePath = $dir . '/TestFile.php';
        $file = new \yii\gii\CodeFile($filePath, '<?php echo "test";');

        $results = '';
        $success = $generator->save([$file], [$file->id => '1'], $results);

        $this->assertTrue($success);
        $this->assertStringContainsString('generated', $results);
        $this->assertFileExists($filePath);

        @unlink($filePath);
        @rmdir($dir);
    }

    public function testSaveCodeFilesSkipped(): void
    {
        $generator = new ConcreteGenerator();
        $file = new \yii\gii\CodeFile('/some/skipped/file.php', '<?php');

        $results = '';
        $success = $generator->save([$file], [], $results);

        $this->assertTrue($success);
        $this->assertStringContainsString('skipped', $results);
    }
}

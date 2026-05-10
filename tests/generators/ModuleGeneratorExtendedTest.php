<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\generators;

use Yii;
use yii\gii\generators\module\Generator as ModuleGenerator;
use yii\helpers\FileHelper;
use yiiunit\gii\TestCase;

class ModuleGeneratorExtendedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockWebApplication();
    }

    public function testSuccessMessageWhenModuleRegistered(): void
    {
        $generator = new ModuleGenerator();
        $generator->moduleID = 'admin';
        $generator->moduleClass = 'app\modules\admin\Module';

        Yii::$app->setModule('admin', ['class' => 'yii\base\Module']);

        $message = $generator->successMessage();
        $this->assertStringContainsString('try it now', $message);
    }

    public function testSuccessMessageWhenModuleNotRegistered(): void
    {
        $generator = new ModuleGenerator();
        $generator->moduleID = 'newmodule';
        $generator->moduleClass = 'app\modules\newmodule\Module';

        $message = $generator->successMessage();
        $this->assertStringContainsString('generated successfully', $message);
        $this->assertStringContainsString('newmodule', $message);
        $this->assertStringContainsString('app\modules\newmodule\Module', $message);
    }

    public function testGenerateCreatesFiles(): void
    {
        FileHelper::createDirectory(Yii::getAlias('@app/modules/admin'));

        $generator = new ModuleGenerator();
        $generator->template = 'default';
        $generator->moduleID = 'admin';
        $generator->moduleClass = 'app\modules\admin\Module';

        $files = $generator->generate();
        $this->assertCount(3, $files);

        $fileNames = array_map(static function ($f) {
            return basename($f->path);
        }, $files);
        sort($fileNames);
        $this->assertContains('Module.php', $fileNames);
        $this->assertContains('DefaultController.php', $fileNames);
        $this->assertContains('index.php', $fileNames);
    }

    public function testValidateModuleClassWithValidInput(): void
    {
        $generator = new ModuleGenerator();
        $generator->moduleClass = 'app\modules\admin\Module';
        $generator->validateModuleClass();
        $this->assertArrayNotHasKey('moduleClass', $generator->getErrors());
    }

    public function testRules(): void
    {
        $generator = new ModuleGenerator();
        $rules = $generator->rules();
        $this->assertNotEmpty($rules);
    }
}

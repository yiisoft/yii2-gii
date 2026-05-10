<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii\generators;

use Yii;
use yii\gii\generators\controller\Generator as ControllerGenerator;
use yii\helpers\FileHelper;
use yiiunit\gii\TestCase;

class ControllerGeneratorSuccessMessageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockWebApplication();
    }

    public function testSuccessMessageWithMatchingNamespace(): void
    {
        Yii::$app->controllerNamespace = 'app\controllers';
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\SiteController';
        $generator->template = 'default';

        $message = $generator->successMessage();
        $this->assertStringContainsString('generated successfully', $message);
    }

    public function testSuccessMessageWithDifferentNamespace(): void
    {
        Yii::$app->controllerNamespace = 'app\controllers';
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\admin\SiteController';
        $generator->template = 'default';

        $message = $generator->successMessage();
        $this->assertStringContainsString('generated successfully', $message);
        $this->assertStringNotContainsString('try it now', $message);
    }

    public function testSuccessMessageWithSubNamespace(): void
    {
        Yii::$app->controllerNamespace = 'app\controllers';
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\admin\SiteController';
        $generator->template = 'default';

        $message = $generator->successMessage();
        $this->assertStringContainsString('try it now', $message);
    }

    public function testSuccessMessageWithIndexAction(): void
    {
        Yii::$app->controllerNamespace = 'app\controllers';
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\PostController';
        $generator->actions = 'index, create';
        $generator->template = 'default';

        $message = $generator->successMessage();
        $this->assertStringContainsString('post', $message);
    }

    public function testSuccessMessageWithoutIndexAction(): void
    {
        Yii::$app->controllerNamespace = 'app\controllers';
        $generator = new ControllerGenerator();
        $generator->controllerClass = 'app\controllers\PostController';
        $generator->actions = 'create, update';
        $generator->template = 'default';

        $message = $generator->successMessage();
        $this->assertStringContainsString('try it now', $message);
    }

    public function testValidateControllerClassWithLeadingBackslash(): void
    {
        FileHelper::createDirectory(Yii::getAlias('@app/controllers'));

        $generator = new ControllerGenerator();
        $generator->template = 'default';
        $generator->controllerClass = '\app\controllers\TestController';

        $valid = $generator->validate();
        $this->assertTrue($valid, print_r($generator->getErrors(), true));
    }

    public function testValidateControllerClassWithoutBackslash(): void
    {
        FileHelper::createDirectory(Yii::getAlias('@app/controllers'));

        $generator = new ControllerGenerator();
        $generator->template = 'default';
        $generator->controllerClass = 'app\controllers\TestController';

        $valid = $generator->validate();
        $this->assertTrue($valid, print_r($generator->getErrors(), true));
    }

    public function testHints(): void
    {
        $generator = new ControllerGenerator();
        $hints = $generator->hints();
        $this->assertArrayHasKey('controllerClass', $hints);
        $this->assertArrayHasKey('actions', $hints);
        $this->assertArrayHasKey('baseClass', $hints);
    }

    public function testAttributeLabels(): void
    {
        $generator = new ControllerGenerator();
        $labels = $generator->attributeLabels();
        $this->assertArrayHasKey('baseClass', $labels);
        $this->assertArrayHasKey('controllerClass', $labels);
        $this->assertArrayHasKey('actions', $labels);
    }

    public function testStickyAttributes(): void
    {
        $generator = new ControllerGenerator();
        $sticky = $generator->stickyAttributes();
        $this->assertContains('baseClass', $sticky);
    }
}

<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii;

use Yii;
use yii\base\Action;
use yii\gii\Generator;
use yii\gii\Module;
use yii\web\Application;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class ModuleExtendedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockWebApplication([
            'components' => [
                'urlManager' => [
                    'enablePrettyUrl' => true,
                    'showScriptName' => false,
                ],
            ],
        ]);
    }

    public function testBootstrapWebApplication(): void
    {
        $module = new Module('gii');
        $module->bootstrap(Yii::$app);

        $rules = Yii::$app->getUrlManager()->rules;
        $this->assertNotEmpty($rules, 'URL rules should be added after bootstrap');

        $found = false;
        foreach ($rules as $rule) {
            if (isset($rule->route) && strpos($rule->route, 'gii') !== false) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Gii URL rules should be added');
    }

    public function testBootstrapConsoleApplication(): void
    {
        $this->destroyApplication();
        $this->mockApplication();

        $module = new Module('gii');
        $module->bootstrap(Yii::$app);

        $this->assertArrayHasKey('gii', Yii::$app->controllerMap);
        $this->assertEquals('yii\gii\console\GenerateController', Yii::$app->controllerMap['gii']['class']);
    }

    public function testBeforeActionWithAllowedIP(): void
    {
        $module = new Module('gii');
        $module->allowedIPs = ['*'];

        $controller = new Controller('default', $module);
        $action = new Action('index', $controller);

        $result = $module->beforeAction($action);
        $this->assertTrue($result);
    }

    public function testBeforeActionWithDeniedIP(): void
    {
        $module = new Module('gii');
        $module->allowedIPs = [];

        $controller = new Controller('default', $module);
        $action = new Action('index', $controller);

        $this->expectException(ForbiddenHttpException::class);
        $module->beforeAction($action);
    }

    public function testBeforeActionGeneratorsCreatedFromConfig(): void
    {
        $module = new Module('gii');
        $module->allowedIPs = ['*'];
        $module->generators = [];

        $controller = new Controller('default', $module);
        $action = new Action('index', $controller);

        $module->beforeAction($action);

        $this->assertArrayHasKey('model', $module->generators);
        $this->assertArrayHasKey('crud', $module->generators);
        $this->assertArrayHasKey('controller', $module->generators);
        $this->assertInstanceOf(Generator::class, $module->generators['model']);
    }

    public function testBeforeActionWithObjectGenerator(): void
    {
        $module = new Module('gii');
        $module->allowedIPs = ['*'];
        $generator = new generators\ConcreteGenerator();
        $module->generators = ['custom' => $generator];

        $controller = new Controller('default', $module);
        $action = new Action('index', $controller);

        $module->beforeAction($action);

        $this->assertSame($generator, $module->generators['custom']);
    }

    public function testCoreGenerators(): void
    {
        $module = new Module('gii');
        $generators = $this->invoke($module, 'coreGenerators');

        $this->assertArrayHasKey('model', $generators);
        $this->assertArrayHasKey('crud', $generators);
        $this->assertArrayHasKey('controller', $generators);
        $this->assertArrayHasKey('form', $generators);
        $this->assertArrayHasKey('module', $generators);
        $this->assertArrayHasKey('extension', $generators);
    }

    public function testResetGlobalSettingsWebApp(): void
    {
        $module = new Module('gii');
        $this->invoke($module, 'resetGlobalSettings');

        $this->assertEquals([], Yii::$app->assetManager->bundles);
    }

    public function testResetGlobalSettingsConsoleApp(): void
    {
        $this->destroyApplication();
        $this->mockApplication();

        $module = new Module('gii');
        $this->invoke($module, 'resetGlobalSettings');

        $this->assertNotInstanceOf(Application::class, Yii::$app);
    }

    public function testDefaultVersionWithExtension(): void
    {
        Yii::$app->extensions['yiisoft/yii2-gii'] = [
            'name' => 'yiisoft/yii2-gii',
            'version' => '2.2.7',
        ];

        $module = new Module('gii');
        $this->assertEquals('2.2.7', $module->getVersion());
    }

    public function testDefaultVersionWithoutExtension(): void
    {
        Yii::$app->extensions = [];

        $module = new Module('gii');
        $version = $module->getVersion();
        $this->assertIsString($version);
    }

    public function testNewFileMode(): void
    {
        $module = new Module('gii');
        $this->assertEquals(0666, $module->newFileMode);
    }

    public function testNewDirMode(): void
    {
        $module = new Module('gii');
        $this->assertEquals(0777, $module->newDirMode);
    }

    public function testAllowedIPsDefault(): void
    {
        $module = new Module('gii');
        $this->assertEquals(['127.0.0.1', '::1'], $module->allowedIPs);
    }

    public function testControllerNamespace(): void
    {
        $module = new Module('gii');
        $this->assertEquals('yii\gii\controllers', $module->controllerNamespace);
    }
}

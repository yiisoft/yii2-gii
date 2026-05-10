<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii;

use ReflectionClass;
use ReflectionException;
use RuntimeException;
use yii\di\Container;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the base class for all yii framework unit tests.
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Clean up after test.
     * By default the application created with [[mockApplication]] will be destroyed.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication(array $config = [], string $appClass = '\yii\console\Application'): void
    {
        $runtimePath = __DIR__ . '/runtime';
        $appPath = $runtimePath . '/app';
        if (!is_dir($appPath) && !mkdir($appPath, 0777, true) && !is_dir($appPath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $appPath));
        }
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => $appPath,
            'runtimePath' => $runtimePath,
            'vendorPath' => dirname(__DIR__) . '/vendor',
        ], $config));
    }

    protected function mockWebApplication(array $config = [], string $appClass = '\yii\web\Application'): void
    {
        $runtimePath = __DIR__ . '/runtime';
        $appPath = $runtimePath . '/app';
        if (!is_dir($appPath) && !mkdir($appPath, 0777, true) && !is_dir($appPath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $appPath));
        }
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => $appPath,
            'runtimePath' => $runtimePath,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
            ],
        ], $config));
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication(): void
    {
        Yii::$app = null;
        Yii::$container = new Container();
    }

    /**
     * Invokes object method, even if it is private or protected.
     * @param object $object object.
     * @param string $method method name.
     * @param array $args method arguments
     * @return mixed method result
     * @throws ReflectionException
     */
    protected function invoke(object $object, string $method, array $args = [])
    {
        $classReflection = new ReflectionClass(get_class($object));
        $methodReflection = $classReflection->getMethod($method);
        if (PHP_VERSION_ID < 80100) {
            $methodReflection->setAccessible(true);
        }
        return $methodReflection->invokeArgs($object, $args);
    }
}

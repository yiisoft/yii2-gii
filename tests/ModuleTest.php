<?php

namespace yiiunit\gii;

use Yii;
use yii\gii\Module;

class ModuleTest extends TestCase
{
    public function testDefaultVersion()
    {
        $this->mockApplication();
        Yii::$app->extensions['yiisoft/yii2-gii'] = [
            'name' => 'yiisoft/yii2-gii',
            'version' => '2.0.6',
        ];

        $module = new Module('gii');

        $this->assertEquals('2.0.6', $module->getVersion());
    }

    /**
     * Data provider for [[testCheckAccess()]]
     * @return array test data
     */
    public function dataProviderCheckAccess()
    {
        return [
            [
                [],
                '10.20.30.40',
                false
            ],
            [
                ['10.20.30.40'],
                '10.20.30.40',
                true
            ],
            [
                ['*'],
                '10.20.30.40',
                true
            ],
            [
                ['10.20.30.*'],
                '10.20.30.40',
                true
            ],
            [
                ['10.20.30.*'],
                '10.20.40.40',
                false
            ],
            [
                ['172.16.0.0/12'],
                '172.15.1.2', // "below" CIDR range
                false
            ],
            [
                ['172.16.0.0/12'],
                '172.16.0.0', // in CIDR range
                true
            ],
            [
                ['172.16.0.0/12'],
                '172.22.33.44', // in CIDR range
                true
            ],
            [
                ['172.16.0.0/12'],
                '172.31.255.255', // in CIDR range
                true
            ],
            [
                ['172.16.0.0/12'],
                '172.32.1.2',  // "above" CIDR range
                false
            ],
        ];
    }

    // Tests :

    /**
     * @dataProvider dataProviderCheckAccess
     *
     * @param array $allowedIPs
     * @param string $userIp
     * @param bool $expectedResult
     * @throws \ReflectionException
     */
    public function testCheckAccess(array $allowedIPs, $userIp, $expectedResult)
    {
        $module = new Module('gii');
        $module->allowedIPs = $allowedIPs;
        $this->mockWebApplication();
        $_SERVER['REMOTE_ADDR'] = $userIp;
        $this->assertEquals($expectedResult, $this->invoke($module, 'checkAccess'));
    }
}

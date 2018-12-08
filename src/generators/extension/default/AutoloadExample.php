<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace <?= substr($generator->namespace, 0, -1) ?>;

/**
 * This is just an example.
 */
class AutoloadExample extends \yii\base\Widget
{
    public function run()
    {
        return "Hello!";
    }
}

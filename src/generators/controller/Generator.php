<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yii\gii\generators\controller;

use Yii;
use yii\gii\CodeFile;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\web\Controller;

/**
 * This generator will generate a controller and one or a few action view files.
 *
 * @property-read array $actionIDs An array of action IDs entered by the user.
 * @property-read string $controllerFile The controller class file path.
 * @property-read string $controllerID The controller ID.
 * @property-read string $controllerNamespace The namespace of the controller class.
 * @property-read string $controllerSubPath The controller sub path.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\Generator
{
    /**
     * @var string the controller class name
     */
    public string $controllerClass;
    /**
     * @var string the controller's view path
     */
    public string $viewPath;
    /**
     * @var string the base class of the controller
     */
    public string $baseClass = Controller::class;
    /**
     * @var string list of action IDs separated by commas or spaces
     */
    public string $actions = 'index';

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Controller Generator';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return 'This generator helps you to quickly generate a new controller class with
            one or several controller actions and their corresponding views.';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['controllerClass', 'actions', 'baseClass'], 'trim'],
            [['controllerClass', 'baseClass'], 'required'],
            ['controllerClass', 'match', 'pattern' => '/^[\w\\\\]*Controller$/', 'message' => 'Only word characters and backslashes are allowed, and the class name must end with "Controller".'],
            ['controllerClass', 'validateNewClass'],
            ['baseClass', 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            ['actions', 'match', 'pattern' => '/^[a-z][a-z0-9\\-,\\s]*$/', 'message' => 'Only a-z, 0-9, dashes (-), spaces and commas are allowed.'],
            ['viewPath', 'safe'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'baseClass' => 'Base Class',
            'controllerClass' => 'Controller Class',
            'viewPath' => 'View Path',
            'actions' => 'Action IDs',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates(): array
    {
        return [
            'controller.php',
            'view.php',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function stickyAttributes(): array
    {
        return ['baseClass'];
    }

    /**
     * {@inheritdoc}
     */
    public function hints(): array
    {
        return [
            'controllerClass' => 'This is the name of the controller class to be generated. You should
                provide a fully qualified namespaced class (e.g. <code>app\controllers\PostController</code>),
                and class name should be in CamelCase ending with the word <code>Controller</code>. Make sure the class
                is using the same namespace as specified by your application\'s controllerNamespace property.',
            'actions' => 'Provide one or multiple action IDs to generate empty action method(s) in the controller. Separate multiple action IDs with commas or spaces.
                Action IDs should be in lower case. For example:
                <ul>
                    <li><code>index</code> generates <code>actionIndex()</code></li>
                    <li><code>create-order</code> generates <code>actionCreateOrder()</code></li>
                </ul>',
            'viewPath' => 'Specify the directory for storing the view scripts for the controller. You may use path alias here, e.g.,
                <code>/var/www/basic/controllers/views/order</code>, <code>@app/views/order</code>. If not set, it will default
                to <code>@app/views/ControllerID</code>',
            'baseClass' => 'This is the class that the new controller class will extend from. Please make sure the class exists and can be autoloaded.',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function successMessage(): string
    {
        return 'The controller has been generated successfully.' . $this->getLinkToTry();
    }

    /**
     * This method returns a link to try controller generated
     * @see https://github.com/yiisoft/yii2-gii/issues/182
     * @return string
     * @since 2.0.6
     */
    private function getLinkToTry(): string
    {
        if (strpos($this->controllerNamespace, Yii::$app->controllerNamespace) !== 0) {
            return '';
        }

        $actions = $this->getActionIDs();
        if (in_array('index', $actions, true)) {
            $route = $this->getControllerSubPath() . $this->getControllerID() . '/index';
        } else {
            $route = $this->getControllerSubPath() . $this->getControllerID() . '/' . reset($actions);
        }
        return ' You may ' . Html::a('try it now', Yii::$app->getUrlManager()->createUrl($route), ['target' => '_blank', 'rel' => 'noopener noreferrer']) . '.';
    }

    /**
     * {@inheritdoc}
     */
    public function generate(): array
    {
        $files = [];

        $files[] = new CodeFile(
            $this->getControllerFile(),
            $this->render('controller.php')
        );

        foreach ($this->getActionIDs() as $action) {
            $files[] = new CodeFile(
                $this->getViewFile($action),
                $this->render('view.php', ['action' => $action])
            );
        }

        return $files;
    }

    /**
     * Normalizes [[actions]] into an array of action IDs.
     * @return array an array of action IDs entered by the user
     */
    public function getActionIDs(): array
    {
        $actions = array_unique(preg_split('/[\s,]+/', $this->actions, -1, PREG_SPLIT_NO_EMPTY));
        sort($actions);

        return $actions;
    }

    /**
     * @return string the controller class file path
     */
    public function getControllerFile(): string
    {
        return Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\'))) . '.php';
    }

    /**
     * @return string the controller ID
     */
    public function getControllerID(): string
    {
        $name = StringHelper::basename($this->controllerClass);
        return Inflector::camel2id(substr($name, 0, strlen($name) - 10));
    }

    /**
     * This method will return sub path for controller if it
     * is located in subdirectory of application controllers dir
     * @see https://github.com/yiisoft/yii2-gii/issues/182
     * @since 2.0.6
     * @return string the controller sub path
     */
    public function getControllerSubPath(): string
    {
        $subPath = '';
        $controllerNamespace = $this->getControllerNamespace();
        if (strpos($controllerNamespace, Yii::$app->controllerNamespace) === 0) {
            $subPath = substr($controllerNamespace, strlen(Yii::$app->controllerNamespace));
            $subPath = ($subPath !== '') ? str_replace('\\', '/', substr($subPath, 1)) . '/' : '';
        }
        return $subPath;
    }

    /**
     * @param string $action the action ID
     * @return string the action view file path
     */
    public function getViewFile($action): string
    {
        if (empty($this->viewPath)) {
            return Yii::getAlias('@app/views/' . $this->getControllerSubPath() . $this->getControllerID() . "/$action.php");
        }

        return Yii::getAlias(str_replace('\\', '/', $this->viewPath) . "/$action.php");
    }

    /**
     * @return string the namespace of the controller class
     */
    public function getControllerNamespace(): string
    {
        $name = StringHelper::basename($this->controllerClass);
        return ltrim(substr($this->controllerClass, 0, - (strlen($name) + 1)), '\\');
    }
}

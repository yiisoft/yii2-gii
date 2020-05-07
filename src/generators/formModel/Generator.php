<?php


namespace yii\gii\generators\formModel;


use Yii;
use yii\base\Model;
use yii\gii\CodeFile;
use yii\validators\Validator;

/**
 *
 * @property void $name
 */
class Generator extends \yii\gii\Generator
{
    public $ns;
    public $base_class = 'yii\base\Model';
    public $class_name;
    public $properties;
    public $rules;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['ns', 'base_class', 'class_name', 'properties'], 'required'],
            [['ns', 'base_class', 'class_name'], 'filter', 'filter' => 'trim'],
            [['ns', ], 'filter', 'filter' => function ($value) { return trim($value, '\\'); }],
            [['base_class'], 'validateClass', 'params' => ['extends' => Model::className()]],
            [['properties'], 'each', 'rule' => ['required']],
            [['properties'], function ($attribute, $params, $validator) {
                if (count(array_unique($this->$attribute)) !== count($this->$attribute)) {
                    $this->addError($attribute, 'Duplicate values found');
                }
            }],
            [['properties'], 'each', 'rule' => ['string']],
            [['properties'], 'each', 'rule' => ['filter', 'filter' => 'trim']],
            [['properties'], 'each', 'rule' => ['match', 'pattern' => '/^[A-Za-z\_]\w+$/', 'message' => 'Only word characters are allowed according to PHP variable rule.']],
            [['rules', 'properties'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), ['ns' => 'Name Space']);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'Form Model Generator';
    }

    public function getDescription()
    {
        return 'This generator generates form model to validate the form generated against this Model. Next use <strong>Form Generator</strong>';
    }

    public function hints()
    {
        return array_merge(parent::hints(), [
            'ns' => 'This is the namespace of the FormModel class to be generated, e.g., <code>app\models</code>',
            'base_class' => 'This is the base class of the new FormModel class. It should be a fully qualified namespaced class name.  e.g., <code>app\models\BaseModel</code>',
            'class_name' => 'This is the name of the FormModel class to be generated.
            The class name should not contain the namespace part as it is specified in "Namespace".',
            'properties' => 'Use <code>plus</code>, <code>minus</code> icon to add or remove property of the form model',
        ]);
    }

    public function requiredTemplates()
    {
        return ['model.php'];
    }

    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['ns', 'base_class']);
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $params = [
            'ns' => $this->ns,
            'class_name' => $this->class_name,
            'base_class' => '\\' . $this->base_class,
            'properties' => $this->properties,
        ];
        $rules = [];
        foreach (Validator::$builtInValidators as $validator => $validatorClass) {
            foreach ($this->properties as $index => $property) {
                if (is_array($this->rules[$index]) && in_array($validator, $this->rules[$index])) {
                    $rules[$validator][] = $property;
                }
            }
        }
        $params['rules'] = $rules;

        $validator_props = [
            'compare' => ",  /** @todo add `compareAttribute` or `compareValue` here */",
            'default' => ", 'value' => null /** @todo change the default value */",
            'each' => ", 'rule' => [/** @todo add your rule here for array attribute */]",
            'exist' => ",  [/** @todo adjust your target to check the existence */]",
            'filter' => ", 'filter' => null /** @todo Change the filter function */",
            'in' => ", 'range' => [/** @todo Put your range here */]",
            'match' => ", 'pattern' => '/^$/i' /** @todo Put your regex here */",
        ];
        $params['validator_props'] = $validator_props;

        $files = [];
        $files[] = new CodeFile(
            Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $this->class_name . '.php',
            $this->render('model.php', $params)
        );

        return $files;
    }

}

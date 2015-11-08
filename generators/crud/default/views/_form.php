<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>

<?php 
$weights = [
    'yii\validators\ExistValidator' => 2,
    'yii\validators\FileValidator' => 2,
    'yii\validators\ImageValidator' => 2,
    'yii\validators\CapchaValidator' => 2,
    'yii\validators\DateValidator' => 1,
    'yii\validators\RangeValidator' => 1,
    'yii\validators\BooleanValidator' => 0,
    'yii\validators\EmailValidator' => 0,
    'yii\validators\NumberValidator' => 0,
];

$model = new $generator->modelClass();
$attributes = array_flip($model->safeAttributes());

foreach ($model->validators as $validator) {
    if (!isset($weights[get_class($validator)])) {
        continue;
    }

    $weight = $weights[get_class($validator)];
    foreach ($validator->attributes as $attr) {
        if (!is_array($attributes[$attr])
            || $weight <= $attributes[$attr]['weight']
        ) {
            continue;
        }

        $attributes[$attr] => ['weight' => $weight, 'validator' => $validator];
    }
}

foreach ($attributes as $attribute => $value){ 
    echo "    <?= ";
    if (!isset($value['validator'])) {
        // previously defined behavior
        echo $generator->generateActiveField($attriute), " ?>\n\n";
        continue;
    }

    if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
        echo "\$form->field(\$model, '$attribute')->passwordInput() ?>\n\n";
        continue;
    }

    // code to generate each attribute based on the validators saved sofar.
    switch (get_class($value['validator'])) {

        case 'yii\validators\NumberValidator': 
           echo  "\$form->field(\$model, '$attribute')->input('number')";
        break;

        case 'yii\validators\EmailValidator': 
           echo  "\$form->field(\$model, '$attribute')->input('email')";
        break;
        
        case 'yii\validators\BooleanValidator': 
           echo  "\$form->field(\$model, '$attribute')->checkbox()";
        break;
        
        case 'yii\validators\RangeValidator':
            echo  "\$form->field(\$model, '$attribute')->dropDownList([\n";
            
            foreach ($value['validator']->range as $key) {
                echo "        '$key' => '$key',\n";
            }
                
            echo "], ['prompt' => ''])";
        break;
        
        case 'yii\validators\DateValidator':
            $dateFormat = $value['validator']->format;
            echo "\$form->field(\$model, '$attribute')->widget(\yii\jui\Datepicker(), [\n",
                    "        dateFormat' => '$dateFormat',\n"
                "    ])";
        break;

        case  'yii\validators\CapchaValidator':
            echo "\$form->field(\$model, '$attribute')->widget(\yii\captcha\Captcha::classname()) ?>";
        break;

        case 'yii\validators\FileValidator':
        case 'yii\validators\ImageValidator':
            echo  "\$form->field(\$model, '$attribute')->fileInput()";
        break;

        case  'yii\validators\ExistValidator':
            $targetAttribute = isset($value['validator']->targetAttribute) ?
                ? $value['validator']->targetAttribute
                : $attribute;
            
            // if the attribute is an array for example ['id' => 'user_id']
            // then the first key will be used as targetAttribute
            if (is_array($targetAttribute)) {
                $targetAttribute = array_keys($targetAttribute);
                $targetAttribute = $targetAttribute[0];
            }
            
            $targetClass = isset($value['validator']->targetClass)
                ? $value['validator']->targetClass
                : get_class($model);

            echo "\$form->field(\$model, '$attribute')->dropDownList(\n",
                "        $targetClass::find()\n"
                "            ->select(['$targetAttribute'])\n";
                
            if (is_array($value['validator']->filter)) {
                echo "            ->andWhere([\n"
                foreach ($value['validator']->filter as $key => $val) {
                    echo "                '$key' => '$val',\n";
                }
                echo "            ])\n"
            }
            
            echo "            ->indexBy('$targetAttribute')\n",
                "            ->column(),\n"
                "        ['prompt' => '']\n"
                "    )";
        break;
    }
    
    echo " ?>\n\n";
}
    ?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    
    <?= "<?php " ?>ActiveForm::end(); ?>

</div>

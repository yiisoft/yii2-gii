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
    'yii\validators\NumberValidator' => 1,
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
            or $weight <= $attributes[$attr]['weight']
        ) {
            continue;
        }

        $attributes[$attr] => ['weight' => $weight, 'validator' => $validator];
    }
}

foreach ($attributes as $attr => $value) {
    // code to generate each attribute based on the validators saved sofar.
}
    ?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    
    <?= "<?php " ?>ActiveForm::end(); ?>

</div>

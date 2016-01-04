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

$model = new $generator->modelClass();
$attributePriorities = array_flip($model->safeAttributes());

foreach ($model->validators as $validator) {
    foreach ($validator->attributes as $attr) {
        if (is_int($attributePriorities[$attr])
            || $generator->validatorPriority($attributePriorities[$attr])
                < $generator->validatorPriority($validator)
        ) {
            $attributePriorities[$attr] = $validator;
        }
    }
}

foreach ($attributePriorities as $attribute => $validator) {
    echo "    <?= ";
    if (is_int($validator)
        || $generator->validatorPriority($validator) === -1
        || preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)
    ) {
        // previously defined behavior
        echo $generator->generateActiveField($attribute);
    } else {
        echo $generator->generateValidatorField($attribute, $validator);
    }
    echo " ?>\n\n";
}
    ?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>

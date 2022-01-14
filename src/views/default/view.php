<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\gii\components\ActiveField;
use yii\gii\CodeFile;

/** @var yii\web\View $this */
/** @var yii\gii\Generator $generator */
/** @var string $id panel ID */
/** @var yii\widgets\ActiveForm $form */
/** @var string $results */
/** @var bool $hasError */
/** @var CodeFile[] $files */
/** @var array $answers */

$this->title = $generator->getName();
$templates = [];
foreach ($generator->templates as $name => $path) {
    $templates[$name] = "$name ($path)";
}
?>
<div class="default-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= $generator->getDescription() ?></p>

    <?php $form = ActiveForm::begin([
        'id' => "$id-generator",
        'successCssClass' => 'is-valid',
        'errorCssClass' => 'is-invalid',
        'validationStateOn' => ActiveForm::VALIDATION_STATE_ON_INPUT,
        'fieldConfig' => [
            'class' => ActiveField::className(),
            'hintOptions' => ['tag' => 'small', 'class' => 'form-text text-muted'],
            'errorOptions' => ['class' => 'invalid-feedback']
        ],
    ]); ?>
        <div class="row">
            <div class="col-lg-8 col-md-10" id="form-fields">
                <?= $this->renderFile($generator->formView(), [
                    'generator' => $generator,
                    'form' => $form,
                ]) ?>
                <?= $form->field($generator, 'template')
                    ->sticky()
                    ->hint('Please select which set of the templates should be used to generated the code.')
                    ->label('Code Template')
                    ->dropDownList($templates) ?>
                <div class="form-group">
                    <?= Html::submitButton('Preview', ['name' => 'preview', 'class' => 'btn btn-primary']) ?>

                    <?php if (isset($files)): ?>
                        <?= Html::submitButton('Generate', ['name' => 'generate', 'class' => 'btn btn-success']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php
        if (isset($results)) {
            echo $this->render('view/results', [
                'generator' => $generator,
                'results' => $results,
                'hasError' => $hasError,
            ]);
        } elseif (isset($files)) {
            echo $this->render('view/files', [
                'id' => $id,
                'generator' => $generator,
                'files' => $files,
                'answers' => $answers,
            ]);
        }
        ?>
    <?php ActiveForm::end(); ?>
</div>

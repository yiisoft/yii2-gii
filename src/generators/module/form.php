<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */
/** @var yii\gii\generators\module\Generator $generator */

?>
<div class="module-form">
<?php
    echo $form->field($generator, 'moduleClass');
    echo $form->field($generator, 'moduleID');
?>
</div>

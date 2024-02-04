<?php
/**
 * @var \yii\web\View $this
 * @var \yii\widgets\ActiveForm $form
 * @var \yii\gii\generators\formModel\Generator $generator
 */

use yii\gii\FormModelAsset;
use yii\validators\Validator;


FormModelAsset::register($this);

$tab = ['id' => ['val' => 'heading', 'index' => true]];
$tab_content = [
    'id' => ['val' => 'collapse-', 'index' => true],
    'aria-labelledby' => $tab['id'],
];
$prop_attr = [
    'accordion' => [
        'data-index' => ['val' => '', 'index' => true],
        'tab' => $tab,
        'tab_content' => $tab_content
    ],
];

$prop_count = !empty($generator->properties) && is_array($generator->properties) ? count($generator->properties) - 1 : 0;
?>

<?= $form->field($generator, 'ns') ?>
<?= $form->field($generator, 'base_class') ?>
<?= $form->field($generator, 'class_name') ?>
<label><?php echo $generator->getAttributeLabel('properties'); ?></label>
<div class="accordion" id="accordion" role="tablist" aria-multiselectable="false">
    <?php for ($i = 0; $i <= $prop_count; $i++) { ?>
        <?php
        $tab_heading_id = $prop_attr['accordion']['tab']['id']['val'] . ($prop_attr['accordion']['tab']['id']['index'] ? $i : '');
        $tab_content_id = $prop_attr['accordion']['tab_content']['id']['val'] . ($prop_attr['accordion']['tab_content']['id']['index'] ? $i : '');
        ?>
        <div class="card mt-2 bg-light js-field_row border-0" data-index="<?php echo $i; ?>" id="accordion-<?php echo $i; ?>">

            <div class="card-header text-white bg-secondary p-2" id="<?php echo $tab_heading_id ?>">
                <div class="row">
                    <div class="col-md-9 collapsed" style="line-height: 34px; display: block;cursor: pointer;" data-toggle="collapse-custom" data-target="#accordion-<?php echo $i; ?>">
                        <a role="button" class="card-title">
                            <?php echo "property #<span data-index='{$i}'>{$i}</span>: "; ?><strong><span id="prop_name_<?php echo $i ?>"><?php echo isset($generator->properties[$i]) ? $generator->properties[$i] : ''; ?></span></strong>
                        </a>
                    </div>
                    <div class="col-md-3 text-right">
                        <a class="btn btn-success" data-js-add=".js-field_row">+</a>
                        <a class="btn btn-danger" data-js-remove=".js-field_row">-</a>
                    </div>
                </div>
            </div>
            <div id="<?php echo $tab_content_id ?>" class="card-body border border-secondary">
                <div class="form-horizontal" style="margin-top:15px;">
                    <div class="form-group">
                        <label class="col-md-4 control-label"><?php echo $generator->getAttributeLabel('properties'); ?></label>
                        <div class="col-md-7">
                            <?php
                            echo $form->field($generator, "properties[$i]", ['inputOptions' => ['class' => ['js-property-name', 'form-control'], 'data-index' => $i, 'data-update' => '#prop_name_' . $i]])->label(false);
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $validators = array_keys(Validator::$builtInValidators);
                $validators = array_combine($validators, $validators);
                echo $form->field($generator, "rules[$i]", ['options' => ['class' => 'rules-container']])->checkboxList($validators);
                ?>
            </div>
        </div>
    <?php } ?>
</div>

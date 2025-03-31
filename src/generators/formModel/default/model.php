<?php
/**
 * @var $ns string
 * @var $class_name string
 * @var $base_class string
 * @var $properties array
 * @var $rules array
 */
?>
<?php
echo "<?php\n";
?>

namespace <?php echo $ns.";\n"; ?>

class <?php echo $class_name; ?> extends <?php echo $base_class."\n"; ?>
{
<?php foreach ($properties as $property){ ?>
    <?php echo "public \$$property;\n"; ?>
<?php } ?>

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
<?php foreach ($rules as $rule => $attributes){ ?>
            [['<?php echo implode("', '", $attributes) ?>'], <?php echo "'$rule'"; ?><?php echo isset($validator_props[$rule])?$validator_props[$rule]:''; ?>],
<?php } ?>
        ];
    }

}

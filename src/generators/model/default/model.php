<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $properties array list of properties (property => [type, name. comment]) */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var array $enum list of ENUM fields */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($properties as $property => $data): ?>
 * @property <?= "{$data['type']} \${$property}"  . ($data['comment'] ? ' ' . strtr($data['comment'], ["\n" => ' ']) : '') . "\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{

<?php
if(!empty($enum)){
    ?>
    /**
    * ENUM field values
    */
<?php
    foreach($enum as $column_name => $column_data){
        foreach ($column_data['values'] as $enum_value){
            echo '    const ' . $enum_value['const_name'] . ' = \'' . $enum_value['value'] . '\';' . PHP_EOL;
        }
    }
}
?>

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [<?= empty($rules) ? '' : ("\n            " . implode(",\n            ", $rules) . ",\n        ") ?>];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>

    /**
     * Gets query for [[<?= $name ?>]].
     *
     * @return <?= $relationsClassHints[$name] . "\n" ?>
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * {@inheritdoc}
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>

<?php if ($enum): ?>
<?php
foreach($enum as $column_name => $column_data){
    ?>

    /**
    * column <?php echo $column_name?> ENUM value labels
    * @return array
    */
    public static function <?php echo $column_data['func_opts_name']?>()
    {
        return [
<?php
    foreach($column_data['values'] as $k => $value){
        if ($generator->enableI18N) {
            echo '            '.'self::' . $value['const_name'] . ' => Yii::t(\'' . $generator->messageCategory . '\', \'' . $value['value'] . "'),\n";
        } else {
            echo '            '.'self::' . $value['const_name'] . ' => \'' . $value['value'] . "',\n";
        }
    }
    ?>
        ];
    }
<?php
}


    foreach($enum as $column_name => $column_data){
?>

    /**
    * @return string
    */
    public function <?=$column_data['displayFunctionPrefix']?>()
    {
        return self::<?=$column_data['func_opts_name']?>()[$this-><?=$column_name?>];
    }
<?php
        foreach ($column_data['values'] as $enum_value){
?>

    /**
    * @return bool
    */
    public function <?=$column_data['isFunctionPrefix'].$enum_value['isFunctionSuffix']?>()
    {
        return $this-><?=$column_name?> === self::<?=$enum_value['const_name']?>;
    }
<?php
        }
    }
endif;
?>

}

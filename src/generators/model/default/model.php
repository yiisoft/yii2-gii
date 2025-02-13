<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/** @var $enum array list of ENUM fields */
/** @var yii\web\View $this */
/** @var yii\gii\generators\model\Generator $generator */
/** @var string $tableName full table name */
/** @var string $className class name */
/** @var string $queryClassName query class name */
/** @var yii\db\TableSchema $tableSchema */
/** @var array $properties list of properties (property => [type, name. comment]) */
/** @var string[] $labels list of attribute labels (name => label) */
/** @var string[] $rules list of validation rules */
/** @var array $relations list of relations (name => relation declaration) */
/** @var array $relationsClassHints */

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

<?php if (!empty($enum)): ?>
    /**
     * ENUM field values
     */
<?php
    foreach($enum as $columnName => $columnData) {
        foreach ($columnData['values'] as $enumValue){
            echo '    const ' . $enumValue['constName'] . ' = \'' . $enumValue['value'] . '\';' . PHP_EOL;
        }
    }
endif
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
<?php     foreach ($enum as $columnName => $columnData): ?>

    /**
     * column <?= $columnName ?> ENUM value labels
     * @return string[]
     */
    public static function <?= $columnData['funcOptsName'] ?>()
    {
        return [
<?php         foreach ($columnData['values'] as $k => $value): ?>
<?php
        if ($generator->enableI18N) {
            echo '            self::' . $value['constName'] . ' => Yii::t(\'' . $generator->messageCategory . '\', \'' . $value['value'] . "'),\n";
        } else {
            echo '            self::' . $value['constName'] . ' => \'' . $value['value'] . "',\n";
        }
    ?>
<?php         endforeach; ?>
        ];
    }
<?php     endforeach; ?>
<?php     foreach ($enum as $columnName => $columnData): ?>

    /**
     * @return string
     */
    public function <?= $columnData['displayFunctionPrefix'] ?>()
    {
        return self::<?= $columnData['funcOptsName'] ?>()[$this-><?=$columnName?>];
    }
<?php         foreach ($columnData['values'] as $enumValue): ?>

    /**
     * @return bool
     */
    public function <?= $columnData['isFunctionPrefix'] . $enumValue['functionSuffix'] ?>()
    {
        return $this-><?= $columnName ?> === self::<?= $enumValue['constName'] ?>;
    }

    public function <?= $columnData['setFunctionPrefix'] . $enumValue['functionSuffix'] ?>()
    {
        $this-><?= $columnName ?> = self::<?= $enumValue['constName'] ?>;
    }
<?php         endforeach; ?>
<?php     endforeach; ?>
<?php endif; ?>
}

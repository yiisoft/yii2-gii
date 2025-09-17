<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/** @var $enum EnumGenerator[] list of ENUM fields */
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

use yii\gii\generators\model\EnumGenerator;

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
    foreach($enum as $enumColumn) {
        foreach ($enumColumn->enumConstantList() as $enumConstant){
            echo '    const ' . $enumConstant['constantName'] . ' = \'' . $enumConstant['value'] . '\';' . PHP_EOL;
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
<?php     foreach ($enum as  $enumColumn): ?>

    /**
     * column <?= $enumColumn->getColumnsName() ?> ENUM value labels
     * @return string[]
     */
    public static function <?= $enumColumn->createOptsFunctionName()?>()
    {
        return [
<?php         foreach ($enumColumn->enumConstantList() as $enumConstantData): ?>
<?php
        if ($generator->enableI18N) {
            echo '            self::' . $enumConstantData['constantName'] . ' => Yii::t(\'' . $generator->messageCategory . '\', \'' . $enumConstantData['value'] . "'),\n";
        } else {
            echo '            self::' . $enumConstantData['constantName'] . ' => \'' . $enumConstantData['value'] . "',\n";
        }
    ?>
<?php         endforeach; ?>
        ];
    }
<?php     endforeach; ?>
<?php     foreach ($enum as $enumColumn): ?>

    /**
     * @return string
     */
    public function <?= $enumColumn->createDisplayFunctionName()?>()
    {
        return self::<?= $enumColumn->createOptsFunctionName()?>()[$this-><?=$enumColumn->getColumnsName()?>];
    }
<?php         foreach ($enumColumn->enumConstantList() as $enumConstantData): ?>

    /**
     * @return bool
     */
    public function <?=$enumColumn->createIsFunctionName($enumConstantData['value'])?>()
    {
        return $this-><?=$enumColumn->getColumnsName() ?> === self::<?= $enumConstantData['constantName'] ?>;
    }

    public function <?= $enumColumn->createSetFunctionName($enumConstantData['value'])?>()
    {
        $this-><?=$enumColumn->getColumnsName() ?> = self::<?= $enumConstantData['constantName'] ?>;
    }
<?php         endforeach; ?>
<?php     endforeach; ?>
<?php endif; ?>
}

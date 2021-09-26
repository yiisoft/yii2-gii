<?php

/**
 * @var \yii\web\View $this
 * @var \yii\gii\generators\crud\Generator $generator
 */

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$php7 = PHP_MAJOR_VERSION === 7;
$modelClass = StringHelper::basename($generator->modelClass);
$modelName = Inflector::camel2words($modelClass);

echo "<?php\n";
?>

/**
 * @var \yii\web\View $this
<?= !empty($generator->searchModelClass) ? ' * @var ' . $generator->searchModelClass . " \$searchModel\n" : '' ?>
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

use yii\helpers\Html;
<?php if ($generator->indexWidgetType === 'grid'): ?>
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
<?php else: ?>
use yii\widgets\ListView;
<?php endif; ?>
use <?= $generator->modelClass ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

$this->title = <?= $generator->generateString(Inflector::pluralize($modelName)) ?>;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="<?= Inflector::camel2id($modelClass) ?>-index">

    <h1><?= '<?= ' ?>Html::encode($this->title) ?></h1>

    <p class="action-bar">
        <?= '<?= ' ?>Html::a(<?= $generator->generateString('Create ' . $modelName) ?>, ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= $generator->enablePjax ? '<?php Pjax::begin(); ?>' : '' ?>

<?php if(!empty($generator->searchModelClass)): ?>
    <?= '    <?php ' . ($generator->indexWidgetType === 'grid' ? '// ' : '') ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= '<?= ' ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= $generator->searchModelClass ? "'filterModel' => \$searchModel,\n" : ''; ?>
        'columns' => [
            ['class' => SerialColumn::class<?= $php7 ? '' : 'Name()' ?>],
            <?php
            /** @var \yii\base\Model $model */
            $model = \Yii::createObject($generator->modelClass);
            $pk = $model instanceof \yii\db\ActiveRecord ? $model::primaryKey() : [];
            $count = 1;
            $tableSchema = $generator->getTableSchema();
            $labels = $model->attributeLabels();
            foreach ($model->attributes() as $attribute) {
                if (in_array($attribute, $pk, true)) {
                    continue;
                }
                $format = 'text';
                if ($tableSchema) {
                    $column = $tableSchema->getColumn($attribute);
                    $format = $generator->generateColumnFormat($column);
                }
                $label = isset($labels[$attribute]) ? $labels[$attribute] : Inflector::humanize($attribute);
                echo sprintf("                %s'%s:%s:%s',\n", $count < 6 ? '' : '// ', $attribute, $format, $label);
                $count++;
            }
            ?>
            [
                'class' => ActionColumn::class<?= $php7 ? '' : 'Name()' ?>,
                'urlCreator' => static function ($action, <?= $modelClass ?> $model/*, $key, $index, $column */) {
                    return Url::toRoute([$action, <?= $generator->generateUrlParams() ?>]);
                }
            ]
        ]
    ]); ?>
<?php else: ?>
    <?= '<?= ' ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => static function (<?= $modelClass ?> $model/*, $key, $index, $widget */) {
            return Html::a(
                Html::encode($model-><?= $generator->getNameAttribute() ?>),
                ['view', <?= $generator->generateUrlParams() ?>]
            );
        }
    ]); ?>
<?php endif; ?>

    <?= $generator->enablePjax ? '<?php Pjax::end(); ?>' : '' ?>

</div>

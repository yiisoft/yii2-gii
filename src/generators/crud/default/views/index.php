<?php

/**
 * @var \yii\web\View $this
 * @var \yii\gii\generators\crud\Generator $generator
 */

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$php7 = PHP_MAJOR_VERSION === 7;
$modelClass = StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

/**
 * @var \yii\web\View $this
<?= !empty($generator->searchModelClass) ? " * @var " . $generator->searchModelClass . " \$searchModel\n" : '' ?>
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

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words($modelClass))) ?>;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="<?= Inflector::camel2id($modelClass) ?>-index">

    <h1><?= '<?= ' ?>Html::encode($this->title) ?></h1>

    <p>
        <?= '<?= ' ?>Html::a(<?= $generator->generateString('Create ' . Inflector::camel2words($modelClass)) ?>, ['create'], ['class' => 'btn btn-success']) ?>
    </p>

<?= $generator->enablePjax ? "    <?php Pjax::begin(); ?>\n" : '' ?>
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : '') ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= '<?= ' ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= empty($generator->searchModelClass) ? "'columns' => [\n" : "'filterModel' => \$searchModel,\n        'columns' => [\n"; ?>
            ['class' => SerialColumn::class<?= $php7 ? '' : 'Name()' ?>],
<?php
/** @var \yii\base\Model $model */
$model = new $generator->modelClass();
if ($model instanceof \yii\db\ActiveRecord) {
    $pk = $model::primaryKey();
} else {
    $pk= [];
}
$count = 0;
foreach ($model->attributeLabels() as $attribute => $label) {
    if (in_array($attribute, $pk, true)) {
        continue;
    }
    $format = $generator->generateColumnFormat($attribute);
    echo "            '" . (++$count < 6 ? '' : '// ') . $attribute . ':' . $format . ':' . $label .  "',\n";
}
?>
            [
                'class' => ActionColumn::class<?= $php7 ? '' : 'Name()' ?>,
                'urlCreator' => static function ($action, <?= $modelClass ?> $model/*, $key, $index, ActionColumn $column */) {
                    return Url::toRoute([$action, <?= $generator->generateUrlParams() ?>]);
                }
            ],
        ],
    ]); ?>
<?php else: ?>
    <?= '<?= ' ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => static function (<?= $modelClass ?> $model/*, $key, $index, ListView $widget */) {
            return Html::a(Html::encode($model-><?= $generator->getNameAttribute() ?>), ['view', <?= $generator->generateUrlParams() ?>]);
        },
    ]) ?>
<?php endif; ?>
    
<?= $generator->enablePjax ? "    <?php Pjax::end(); ?>\n" : '' ?>
</div>

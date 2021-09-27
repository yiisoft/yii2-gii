<?php
/**
 * This is the template for generating a CRUD controller class file.
 *
 * @var \yii\web\View $this
 * @var \yii\gii\generators\crud\Generator $generator
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
if (!empty($generator->searchModelClass)) {
    $searchModelClass = StringHelper::basename($generator->searchModelClass);
    if ($modelClass === $searchModelClass) {
        $searchModelAlias = $searchModelClass . 'Search';
    }
}

/** @var $class ActiveRecordInterface */
$class = ltrim($generator->modelClass, '\\');
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();
$isPhp7 = PHP_MAJOR_VERSION === 7;

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use <?= $class ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : '') ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * Controller implements the CRUD actions for `<?= $modelClass ?>` model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    /**
     * @inheritDoc
     */
    public function behaviors()<?= ($php7 ? ': array' : '') . "\n" ?>
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbFilter' => [
                    'class' => VerbFilter::class<?= $php7 ? '' : 'Name()' ?>,
                    'actions' => [
                        'index' => ['GET'],
                        'view' => ['GET'],
                        'create' => ['GET', 'POST'],
                        'update' => ['GET', 'POST'],
                        'delete' => ['POST'],
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all <?= $modelClass ?> models.
     * @return string
     */
    public function actionIndex()<?= ($php7 ? ': string' : '') . "\n" ?>
    {
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
<?php else: ?>
        $dataProvider = Yii::createObject(
            ActiveDataProvider::class<?= $php7 ? '' : 'Name()' ?>,
            [
                'query' => <?= $modelClass ?>::find(),
                /*
                'pagination' => [
                    'pageSize' => 50
                ],
                'sort' => [
                    'defaultOrder' => [
<?php foreach ($pks as $pk): ?>
                        <?= "'$pk' => SORT_DESC,\n" ?>
<?php endforeach; ?>
                    ]
                ]
                */
            ]
        );

        return $this->render('index', ['dataProvider' => $dataProvider]);
<?php endif; ?>
    }

    /**
     * Displays a single <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(<?= $actionParams ?>)<?= ($php7 ? ': string' : '') . "\n" ?>
    {
        return $this->render('view', ['model' => $this->findModel(<?= $actionParams ?>)]);
    }

    /**
     * Creates a new <?= $modelClass ?> model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return Response|string
     */
    public function actionCreate()
    {
        $model = Yii::createObject(<?= $modelClass ?>);

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', <?= $urlParams ?>]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return Response|string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', <?= $urlParams ?>]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(<?= $actionParams ?>): Response
    {
        $this->findModel(<?= $actionParams ?>)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?=                   $modelClass ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>): <?= $modelClass . "\n" ?>
    {
<?php
$condition = [];
foreach ($pks as $pk) {
    $condition[] = "'$pk' => \$$pk";
}
$condition = '[' . implode(', ', $condition) . ']';
?>
        $model = <?= $modelClass ?>::findOne(<?= $condition ?>);
        if ($model === null) {
            throw new NotFoundHttpException(<?= $generator->generateString('The requested page does not exist.') ?>);
        }

        return $model;
    }
}

<?php
use yii\bootstrap4\NavBar;
use yii\bootstrap4\Nav;
use yii\helpers\Html;
use yii\gii\assets\GiiAsset;

/* @var $this \yii\web\View */
/* @var $content string */

$asset = GiiAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="none">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <div class="container-fluid wrap">
        <?php $this->beginBody() ?>
        <?php
        NavBar::begin([
            'brandLabel' => Html::img($asset->baseUrl . '/logo.png'),
            'brandUrl' => ['default/index'],
            'brandOptions' => ['class' => 'm-0 p-0'],
            'options' => ['class' => 'navbar-expand-lg navbar-dark bg-dark'],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav ml-auto'],
            'items' => [
                ['label' => 'Home', 'url' => ['default/index']],
                ['label' => 'Help', 'url' => 'http://www.yiiframework.com/doc-2.0/ext-gii-index.html'],
                ['label' => 'Application', 'url' => Yii::$app->homeUrl],
            ],
        ]);
        NavBar::end();
        ?>
        <div class="container pt-3 pb-5">
            <?= $content ?>
        </div>
    </div>
    <footer class="footer bg-light">
        <div class="container">
            <p class="float-left">A Product of <a href="http://www.yiisoft.com/">Yii Software LLC</a></p>
            <p class="float-right"><?= Yii::powered() ?></p>
        </div>
    </footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

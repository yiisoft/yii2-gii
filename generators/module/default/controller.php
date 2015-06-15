<?php
/**
 * This is the template for generating a controller class within a module.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace() ?>;

use yii\web\Controller;

/**
 * Default controller for the `<?= $generator->moduleId ?>` module
 */
class DefaultController extends Controller
{
    /**
     * render the default view for the module
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}

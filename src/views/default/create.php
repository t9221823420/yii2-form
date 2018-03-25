<?php

use yii\helpers\Html;

include '_header.php';

/* @var $this yii\web\View */

?>
<div class="<?= "$moduleId $modelId-$actionId" ?>">

    <h1><?= Html::encode( $this->title ) ?></h1>
	
	<?= $this->render( $path . '_form', $_params_ ) ?>

</div>

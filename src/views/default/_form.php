<?php

use powerkernel\tinymce\TinyMce;
use yii\helpers\Html;
use common\components\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\InvestPlan */
/* @var $form yii\widgets\ActiveForm */

$attributes = $model->attributes;

?>

<div class="invest-plan-form">
	
	<?php $form = ActiveForm::begin(); ?>
	
	<?php $fields = $form->fileds( $model,
		method_exists( $model, 'attributeEditList' )
                ? $model->attributeEditList()
                : array_keys( $model->attributes ),
		false
	);
	
	/*
	$fields['body'] = $form->field( $model, 'body' )->widget(
		TinyMce::className(),
		[
			'options' => [
				'id'   => 'editor',
				'rows' => 10,
			],
		]
	);
	*/
    
    foreach( $fields as $field ) {
        print $field;
    }

	?>

    <div class="form-group">
		<?= Html::submitButton( Yii::t( 'app', 'Save'), [ 'class' => 'btn btn-success' ] ) ?>
    </div>
	
	<?php ActiveForm::end(); ?>

</div>
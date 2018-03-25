<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

include '_header.php';

?>
<div class="<?= "$moduleId $modelId-$actionId" ?>">

    <h1><?= Html::encode( $this->title ) ?></h1>
	<?php Pjax::begin(); ?>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
		<?= Html::a( Yii::t( 'app', 'Create ') . $modelTitle, [ 'create' ], [ 'class' => 'btn btn-primary' ] ) ?>
    </p>
	
	<?= GridView::widget( [
		'dataProvider' => $dataProvider,
		//'filterModel' => $searchModel,
		'tableOptions' => [
			'class' => 'table table-striped table-hover',
		],
		
		'columns' => array_merge(
			method_exists( $model, 'attributeIndexList' ) ? $model->attributeIndexList() : array_keys( $model->attributes ),
			[ [ 'class' => 'yii\grid\ActionColumn' ] ]
		),
		
		/*
		'columns' => [
				
				'id',
				'title',
				'rate',
				
				['class' => 'yii\grid\ActionColumn']
		],
		*/
	
	] ); ?>
	<?php Pjax::end(); ?>
</div>

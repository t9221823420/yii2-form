<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 10.04.2018
 * Time: 16:39
 */

namespace yozh\form\traits;

trait ActiveBooleanColumnTrait
{
	public function actionSwitch( $id, $attribute , $value )
	{
		$model = $this->findModel( $id );
		
		if( isset( $model->$attribute ) ) {
			
			$model->setAttribute( $attribute, (int)$value ? false : true );
			
			if( $model->save() ) {
				return true;
			}
		}
		
		throw new \yii\web\NotFoundHttpException();
		
	}
	
}